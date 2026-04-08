<?php

namespace App\Application\UseCases\ListExpenses;

final readonly class ListExpensesInput
{
    public function __construct(
        public string $userId,
        public int $page,
        public int $perPage,
    ) {}
}
