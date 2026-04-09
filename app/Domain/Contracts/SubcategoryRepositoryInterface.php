<?php

namespace App\Domain\Contracts;

interface SubcategoryRepositoryInterface
{
    public function belongsToCategory(string $subcategoryId, string $categoryId): bool;
    public function listByCategoryIdOrderedByName(string $categoryId): array;
    public function create(string $categoryId, string $name): array;
}
