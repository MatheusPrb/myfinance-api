<?php

namespace App\Domain\Contracts;

use App\Application\UseCases\ListExpenses\ListExpensesInput;
use App\Application\UseCases\SummarizeSpending\SummarizeSpendingInput;
use App\Application\UseCases\SummarizeSpendingBySubcategory\SummarizeSpendingBySubcategoryInput;
use App\Domain\Entities\Expense;

interface ExpenseRepositoryInterface
{
    public function create(Expense $expense): Expense;
    public function paginateByUserId(ListExpensesInput $input): array;
    public function findByIdAndUserId(string $expenseId, string $userId): ?Expense;
    public function spendingSummaryByUserId(SummarizeSpendingInput $input): array;
    public function spendingSummaryBySubcategoryByUserId(SummarizeSpendingBySubcategoryInput $input): array;
}
