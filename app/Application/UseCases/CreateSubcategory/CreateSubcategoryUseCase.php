<?php

namespace App\Application\UseCases\CreateSubcategory;

use App\Domain\Contracts\CategoryRepositoryInterface;
use App\Domain\Contracts\SubcategoryRepositoryInterface;
use App\Domain\Exceptions\CategoryNotFoundException;
use App\Messages\Messages;

final class CreateSubcategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categories,
        private SubcategoryRepositoryInterface $subcategories,
    ) {}

    public function execute(CreateSubcategoryInput $input): CreateSubcategoryOutput
    {
        if (! $this->categories->existsById($input->categoryId)) {
            throw new CategoryNotFoundException(Messages::CATEGORY_NOT_FOUND);
        }

        $created = $this->subcategories->create($input->categoryId, $input->name);

        return new CreateSubcategoryOutput($created);
    }
}
