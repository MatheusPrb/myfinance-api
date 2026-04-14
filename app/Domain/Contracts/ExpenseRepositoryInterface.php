<?php

namespace App\Domain\Contracts;

use App\Application\UseCases\ListExpenses\ListExpensesInput;
use App\Domain\Entities\Expense;

interface ExpenseRepositoryInterface
{
    public function create(Expense $expense): Expense;
    public function paginateByUserId(ListExpensesInput $input): array;
    public function findByIdAndUserId(string $expenseId, string $userId): ?Expense;
    public function spendingSummaryByUserId(string $userId, ?string $dateFrom = null, ?string $dateTo = null): array;
}
