<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\SubcategoryRepositoryInterface;
use App\Models\Subcategory as SubcategoryModel;

final class SubcategoryRepository implements SubcategoryRepositoryInterface
{
    public function belongsToCategory(string $subcategoryId, string $categoryId): bool
    {
        return SubcategoryModel::query()
            ->whereKey($subcategoryId)
            ->where('category_id', $categoryId)
            ->exists()
        ;
    }
}
