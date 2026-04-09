<?php

namespace App\Application\UseCases\ListSubcategoriesByCategory;

final readonly class ListSubcategoriesByCategoryOutput
{
    /**
     * @param  list<array{id: string, name: string}>  $items
     */
    public function __construct(
        public array $items,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['items' => $this->items];
    }
}
