<?php

namespace App\Application\UseCases\GetExpenseById;

final readonly class GetExpenseByIdInput
{
    public function __construct(
        public readonly string $userId,
        public readonly string $expenseId,
    ) {}
}
