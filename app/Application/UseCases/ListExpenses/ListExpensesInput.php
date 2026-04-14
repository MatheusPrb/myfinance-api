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
    ) {}
}
