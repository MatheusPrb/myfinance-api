<?php

namespace App\Application\UseCases\CreateExpense;

use App\Domain\Entities\Expense;

final readonly class CreateExpenseOutput
{
    public function __construct(
        public readonly Expense $expense,
    ) {}
}
