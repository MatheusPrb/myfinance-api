<?php

namespace App\Application\UseCases\CreateCategory;

use App\Domain\Contracts\CategoryRepositoryInterface;

final class CreateCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categories,
    ) {}

    public function execute(CreateCategoryInput $input): CreateCategoryOutput
    {
        $created = $this->categories->create($input->name);

        return new CreateCategoryOutput($created);
    }
}
