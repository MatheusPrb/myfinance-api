<?php

namespace App\Application\UseCases\GetExpenseById;

use App\Application\Presenters\ExpensePresenter;
use App\Domain\Entities\Expense;

final readonly class GetExpenseByIdOutput
{
    public function __construct(
        public Expense $expense,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ExpensePresenter::toArray($this->expense);
    }
}
