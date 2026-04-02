<?php

namespace App\Domain\Contracts;

use App\Domain\Entities\Expense;

interface ExpenseRepositoryInterface
{
    public function create(Expense $expense): Expense;
}
