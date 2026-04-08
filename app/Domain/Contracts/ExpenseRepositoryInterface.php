<?php

namespace App\Domain\Contracts;

use App\Domain\Entities\Expense;

interface ExpenseRepositoryInterface
{
    public function create(Expense $expense): Expense;
    public function paginateByUserId(string $userId, int $page, int $perPage): array;
    public function findByIdAndUserId(string $expenseId, string $userId): ?Expense;
    public function spendingSummaryByUserId(string $userId): array;
}
