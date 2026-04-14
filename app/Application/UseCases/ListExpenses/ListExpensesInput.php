<?php

namespace App\Application\UseCases\ListExpenses;

final readonly class ListExpensesInput
{
    public function __construct(
        public string $userId,
        public int $page,
        public int $perPage,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?string $categoryId = null,
        public ?string $subcategoryId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'],
            (int) ($data['page'] ?? 1),
            (int) ($data['per_page'] ?? 15),
            $data['date_from'] ?? null,
            $data['date_to'] ?? null,
            $data['category_id'] ?? null,
            $data['subcategory_id'] ?? null,
        );
    }
}
