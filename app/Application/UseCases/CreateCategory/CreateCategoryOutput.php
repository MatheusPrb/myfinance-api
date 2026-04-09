<?php

namespace App\Application\UseCases\CreateCategory;

final readonly class CreateCategoryOutput
{
    /**
     * @param  array{id: string, name: string}  $category
     */
    public function __construct(
        public array $category,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->category;
    }
}
