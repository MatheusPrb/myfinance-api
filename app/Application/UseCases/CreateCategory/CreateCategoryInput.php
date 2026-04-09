<?php

namespace App\Application\UseCases\CreateCategory;

final readonly class CreateCategoryInput
{
    public function __construct(
        public string $name,
    ) {}
}
