<?php

namespace App\Application\UseCases\CreateExpense;

use App\Domain\Contracts\ExpenseRepositoryInterface;
use App\Domain\Contracts\SubcategoryRepositoryInterface;
use App\Domain\Entities\Expense;
use App\Domain\Exceptions\InvalidSubcategoryForCategoryException;
use App\Messages\Messages;
use App\Helper\Uuid;

final class CreateExpenseUseCase
{
    public function __construct(
        private ExpenseRepositoryInterface $expenses,
        private SubcategoryRepositoryInterface $subcategories,
    ) {}

    public function execute(CreateExpenseInput $input): CreateExpenseOutput
    {
        if ($input->subcategoryId !== null
            && ! $this->subcategories->belongsToCategory($input->subcategoryId, $input->categoryId)) {
            throw new InvalidSubcategoryForCategoryException(Messages::SUBCATEGORY_DOES_NOT_BELONG_TO_CATEGORY);
        }

        $id = Uuid::generate();

        $expense = new Expense(
            $id,
            $input->userId,
            $input->categoryId,
            $input->subcategoryId,
            $input->description,
            $input->value,
        );

        return new CreateExpenseOutput($this->expenses->create($expense));
    }
}
