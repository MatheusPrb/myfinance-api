<?php

namespace App\Application\UseCases\ListCategories;

use App\Domain\Contracts\CategoryRepositoryInterface;

final class ListCategoriesUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categories,
    ) {}

    public function execute(): ListCategoriesOutput
    {
        return new ListCategoriesOutput($this->categories->listAllOrderedByName());
    }
}
