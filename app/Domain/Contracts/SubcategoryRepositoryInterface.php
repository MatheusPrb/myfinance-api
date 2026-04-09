<?php

namespace App\Domain\Contracts;

interface SubcategoryRepositoryInterface
{
    public function belongsToCategory(string $subcategoryId, string $categoryId): bool;

    /**
     * @return list<array{id: string, name: string}>
     */
    public function listByCategoryIdOrderedByName(string $categoryId): array;
}
