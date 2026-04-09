<?php

namespace App\Application\Presenters;

use App\Domain\Entities\Expense;

final class ExpensePresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(Expense $expense): array
    {
        return [
            'id' => $expense->id(),
            'user_id' => $expense->userId(),
            'category_name' => $expense->categoryName() ?? '',
            'subcategory_name' => $expense->subcategoryName(),
            'description' => $expense->description(),
            'value' => $expense->value(),
            'created_at' => $expense->createdAt()?->format(DATE_ATOM),
            'updated_at' => $expense->updatedAt()?->format(DATE_ATOM),
        ];
    }
}
