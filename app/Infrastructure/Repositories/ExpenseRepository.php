<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\ExpenseRepositoryInterface;
use App\Domain\Entities\Expense;
use App\Helper\Money;
use App\Models\Expense as ExpenseModel;

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

    public function paginateByUserId(string $userId, int $page, int $perPage): array
    {
        $paginator = ExpenseModel::query()
            ->where('user_id', $userId)
            ->with(['category', 'subcategory'])
            ->orderByDesc('created_at')
            ->paginate(perPage: $perPage, page: $page)
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

    public function spendingSummaryByUserId(string $userId): array
    {
        $rows = ExpenseModel::query()
            ->where('expenses.user_id', $userId)
            ->join('categories', 'categories.id', '=', 'expenses.category_id')
            ->selectRaw('expenses.category_id as category_id')
            ->addSelect('categories.name as category_name')
            ->selectRaw('SUM(expenses.value) as total')
            ->groupBy('expenses.category_id', 'categories.id', 'categories.name')
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

        $total = ExpenseModel::query()
            ->where('user_id', $userId)
            ->sum('value')
        ;

        return [
            'total' => Money::format($total ?? 0),
            'by_category' => $byCategory,
        ];
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
