<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\ExpenseRepositoryInterface;
use App\Domain\Entities\Expense;
use App\Models\Expense as ExpenseModel;

final class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function create(Expense $expense): Expense
    {
        $model = new ExpenseModel;

        $model->id = $expense->id();
        $model->category_id = $expense->categoryId();
        $model->subcategory_id = $expense->subcategoryId();
        $model->description = $expense->description();
        $model->value = $expense->value();
        $model->save();

        return $this->toEntity($model);
    }

    private function toEntity(ExpenseModel $model): Expense
    {
        return new Expense(
            $model->id,
            $model->category_id,
            $model->subcategory_id,
            $model->description,
            (string) $model->value,
            $model->created_at?->toDateTimeImmutable(),
            $model->updated_at?->toDateTimeImmutable(),
        );
    }
}
