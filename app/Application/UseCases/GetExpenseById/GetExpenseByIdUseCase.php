<?php

namespace App\Application\UseCases\GetExpenseById;

use App\Domain\Contracts\ExpenseRepositoryInterface;
use App\Domain\Exceptions\ExpenseNotFoundException;
use App\Messages\Messages;

final class GetExpenseByIdUseCase
{
    public function __construct(
        private ExpenseRepositoryInterface $expenses,
    ) {}

    public function execute(GetExpenseByIdInput $input): GetExpenseByIdOutput
    {
        $expense = $this->expenses->findByIdAndUserId($input->expenseId, $input->userId);

        if (!$expense) {
            throw new ExpenseNotFoundException(Messages::EXPENSE_NOT_FOUND);
        }

        return new GetExpenseByIdOutput($expense);
    }
}
