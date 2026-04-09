<?php

namespace App\Application\UseCases\ListSubcategoriesByCategory;

final readonly class ListSubcategoriesByCategoryInput
{
    public function __construct(
        public string $categoryId,
    ) {}
}
