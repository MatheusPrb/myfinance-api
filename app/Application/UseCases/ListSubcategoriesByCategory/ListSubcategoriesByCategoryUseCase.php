<?php

namespace App\Application\UseCases\ListSubcategoriesByCategory;

use App\Domain\Contracts\CategoryRepositoryInterface;
use App\Domain\Contracts\SubcategoryRepositoryInterface;
use App\Domain\Exceptions\CategoryNotFoundException;
use App\Messages\Messages;

final class ListSubcategoriesByCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categories,
        private SubcategoryRepositoryInterface $subcategories,
    ) {}

    public function execute(ListSubcategoriesByCategoryInput $input): ListSubcategoriesByCategoryOutput
    {
        if (! $this->categories->existsById($input->categoryId)) {
            throw new CategoryNotFoundException(Messages::CATEGORY_NOT_FOUND);
        }

        return new ListSubcategoriesByCategoryOutput(
            $this->subcategories->listByCategoryIdOrderedByName($input->categoryId),
        );
    }
}
