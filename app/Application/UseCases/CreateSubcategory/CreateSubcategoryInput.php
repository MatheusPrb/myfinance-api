<?php

namespace App\Application\UseCases\CreateSubcategory;

final readonly class CreateSubcategoryInput
{
    public function __construct(
        public string $categoryId,
        public string $name,
    ) {}
}
