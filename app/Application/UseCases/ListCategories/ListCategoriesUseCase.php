<?php

namespace App\Application\UseCases\ListCategories;

use App\Domain\Contracts\CategoryRepositoryInterface;
use App\Domain\Exceptions\CategoryNotFoundException;
use App\Messages\Messages;

final class ListCategoriesUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categories,
    ) {}

    public function execute(): ListCategoriesOutput
    {
        $categories = $this->categories->listAllOrderedByName();

        if (empty($categories)) {
            throw new CategoryNotFoundException(Messages::NO_CATEGORIES_FOUND);
        }

        return new ListCategoriesOutput($categories);
    }
}
