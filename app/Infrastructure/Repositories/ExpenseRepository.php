<?php

namespace App\Infrastructure\Repositories;

use App\Application\UseCases\ListExpenses\ListExpensesInput;
use App\Application\UseCases\SummarizeSpending\SummarizeSpendingInput;
use App\Application\UseCases\SummarizeSpendingBySubcategory\SummarizeSpendingBySubcategoryInput;
use App\Domain\Contracts\ExpenseRepositoryInterface;
use App\Domain\Entities\Expense;
use App\Helper\Money;
use App\Models\Expense as ExpenseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

final class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function create(Expense $expense): Expense
    {
        $model = new ExpenseModel;

        $model->id = $expense->id();
        $model->user_id = $expense->userId();
        $model->category_id = $expense->categoryId();
        $model->subcategory_id = $expense->subcategoryId();
        $model->description = $expense->description();
        $model->value = $expense->value();
        $model->save();
        $model->load(['category', 'subcategory']);

        return $this->toEntity($model);
    }

    public function paginateByUserId(ListExpensesInput $input): array
    {
        $query = ExpenseModel::query()->where('user_id', $input->userId);

        $this->applyCreatedAtBetween($query, $input->dateFrom, $input->dateTo);
        $this->applyListFilters($query, $input);

        $paginator = $query
            ->with(['category', 'subcategory'])
            ->orderByDesc('created_at')
            ->paginate(perPage: $input->perPage, page: $input->page)
            ->withQueryString()
        ;

        $items = [];
        foreach ($paginator->items() as $model) {
            $items[] = $this->toEntity($model);
        }

        return [
            'items' => $items,
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
        ];
    }

    public function findByIdAndUserId(string $expenseId, string $userId): ?Expense
    {
        $model = ExpenseModel::query()
            ->with(['category', 'subcategory'])
            ->where('id', $expenseId)
            ->where('user_id', $userId)
            ->first()
        ;

        return $model !== null ? $this->toEntity($model) : null;
    }

    public function spendingSummaryByUserId(SummarizeSpendingInput $input): array
    {
        $userId = $input->userId;
        $dateFrom = $input->dateFrom;
        $dateTo = $input->dateTo;

        $query = ExpenseModel::query()->where('expenses.user_id', $userId);
        $this->applyCreatedAtBetween($query, $dateFrom, $dateTo, 'expenses.created_at');

        $rows = $query
            ->selectRaw(
                'expenses.category_id as category_id,
                c.name as category_name,
                SUM(expenses.value) as total',
            )
            ->join('categories as c', 'c.id', '=', 'expenses.category_id')
            ->groupBy('expenses.category_id', 'c.id', 'c.name')
            ->orderByRaw('SUM(expenses.value) DESC')
            ->get()
        ;

        $byCategory = [];
        foreach ($rows as $row) {
            $byCategory[] = [
                'category_id' => $row->category_id,
                'category_name' => $row->category_name,
                'total' => Money::format($row->total),
            ];
        }

        $total = $this->sumExpenseValueByUserIdAndDateRange($userId, $dateFrom, $dateTo);

        return [
            'total' => Money::format($total),
            'by_category' => $byCategory,
        ];
    }

    public function spendingSummaryBySubcategoryByUserId(SummarizeSpendingBySubcategoryInput $input): array
    {
        $userId = $input->userId;
        $dateFrom = $input->dateFrom;
        $dateTo = $input->dateTo;

        $query = ExpenseModel::query()
            ->where('expenses.user_id', $userId)
            ->whereNotNull('expenses.subcategory_id')
        ;

        $this->applyCreatedAtBetween($query, $dateFrom, $dateTo, 'expenses.created_at');

        $rows = $query
            ->selectRaw(
                'expenses.category_id as category_id, '
                .'c.name as category_name, '
                .'expenses.subcategory_id as subcategory_id, '
                .'s.name as subcategory_name, '
                .'SUM(expenses.value) as total',
            )
            ->join('categories as c', 'c.id', '=', 'expenses.category_id')
            ->join('subcategories as s', 's.id', '=', 'expenses.subcategory_id')
            ->groupBy(
                'expenses.category_id',
                'c.id',
                'c.name',
                'expenses.subcategory_id',
                's.id',
                's.name',
            )
            ->orderByRaw('SUM(expenses.value) DESC')
            ->get()
        ;

        $bySubcategory = [];
        foreach ($rows as $row) {
            $bySubcategory[] = [
                'category_id' => $row->category_id,
                'category_name' => $row->category_name,
                'subcategory_id' => $row->subcategory_id,
                'subcategory_name' => $row->subcategory_name,
                'total' => Money::format($row->total),
            ];
        }

        $total = $this->sumExpenseValueByUserIdAndDateRange($userId, $dateFrom, $dateTo, true);

        return [
            'total' => Money::format($total),
            'by_subcategory' => $bySubcategory,
        ];
    }

    private function sumExpenseValueByUserIdAndDateRange(
        string $userId,
        ?string $dateFrom,
        ?string $dateTo,
        bool $onlyWithSubcategory = false,
    ): float {
        $query = ExpenseModel::query()->where('user_id', $userId);

        if ($onlyWithSubcategory) {
            $query->whereNotNull('subcategory_id');
        }

        $this->applyCreatedAtBetween($query, $dateFrom, $dateTo);

        return $query->sum('value');
    }

    private function applyCreatedAtBetween(Builder $query, ?string $dateFrom, ?string $dateTo, string $column = 'created_at'): void
    {
        if ($dateFrom === null || $dateTo === null) {
            return;
        }

        $query->whereBetween($column, [
            Carbon::parse($dateFrom)->startOfDay(),
            Carbon::parse($dateTo)->endOfDay(),
        ]);
    }

    private function applyListFilters(Builder $query, ListExpensesInput $input): void
    {
        if ($input->subcategoryId !== null) {
            $query->where('subcategory_id', $input->subcategoryId);

            return;
        }

        if ($input->categoryId !== null) {
            $query->where('category_id', $input->categoryId);
        }
    }

    private function toEntity(ExpenseModel $model): Expense
    {
        $model->loadMissing(['category', 'subcategory']);

        return new Expense(
            $model->id,
            $model->user_id,
            $model->category_id,
            $model->subcategory_id,
            $model->description,
            (string) $model->value,
            $model->created_at?->toDateTimeImmutable(),
            $model->updated_at?->toDateTimeImmutable(),
            $model->category?->name,
            $model->subcategory?->name,
        );
    }
}
