<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\CategoryRepositoryInterface;
use App\Models\Category as CategoryModel;

final class CategoryRepository implements CategoryRepositoryInterface
{
    public function listAllOrderedByName(): array
    {
        $models = CategoryModel::query()
            ->orderBy('name')
            ->get(['id', 'name'])
        ;

        $items = [];
        foreach ($models as $category) {
            $items[] = [
                'id' => $category->id,
                'name' => $category->name,
            ];
        }

        return $items;
    }

    public function existsById(string $id): bool
    {
        return CategoryModel::query()->where('id', $id)->exists();
    }

    public function create(string $name): array
    {
        $model = CategoryModel::query()->create(['name' => $name]);

        return [
            'id' => $model->id,
            'name' => $model->name,
        ];
    }
}
