<?php

namespace App\Domain\Contracts;

interface SubcategoryRepositoryInterface
{
    public function belongsToCategory(string $subcategoryId, string $categoryId): bool;
}
