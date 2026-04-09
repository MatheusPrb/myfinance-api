<?php

namespace App\Application\UseCases\CreateSubcategory;

final readonly class CreateSubcategoryOutput
{
    /**
     * @param  array{id: string, name: string, category_id: string}  $subcategory
     */
    public function __construct(
        public array $subcategory,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->subcategory;
    }
}
