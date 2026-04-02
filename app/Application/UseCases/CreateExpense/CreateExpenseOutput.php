<?php

namespace App\Application\UseCases\CreateExpense;

use App\Domain\Entities\Expense;

final readonly class CreateExpenseOutput
{
    public function __construct(
        public readonly Expense $expense,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->expense->id(),
            'category_id' => $this->expense->categoryId(),
            'subcategory_id' => $this->expense->subcategoryId(),
            'description' => $this->expense->description(),
            'value' => $this->expense->value(),
            'created_at' => $this->expense->createdAt()?->format(DATE_ATOM),
            'updated_at' => $this->expense->updatedAt()?->format(DATE_ATOM),
        ];
    }
}
