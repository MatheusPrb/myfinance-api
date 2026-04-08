<?php

namespace App\Application\UseCases\GetExpenseById;

final readonly class GetExpenseByIdInput
{
    public function __construct(
        public string $userId,
        public string $expenseId,
    ) {}
}
