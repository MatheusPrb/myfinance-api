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

    public function listByCategoryIdOrderedByName(string $categoryId): array
    {
        $models = SubcategoryModel::query()
            ->where('category_id', $categoryId)
            ->orderBy('name')
            ->get(['id', 'name'])
        ;

        $items = [];
        foreach ($models as $subcategory) {
            $items[] = [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
            ];
        }

        return $items;
    }

    public function create(string $categoryId, string $name): array
    {
        $model = SubcategoryModel::query()->create([
            'category_id' => $categoryId,
            'name' => $name,
        ]);

        return [
            'id' => $model->id,
            'name' => $model->name,
            'category_id' => $model->category_id,
        ];
    }
}
