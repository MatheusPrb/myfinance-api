<?php

namespace App\Application\UseCases\SummarizeSpending;

final readonly class SummarizeSpendingInput
{
    public function __construct(
        public readonly string $userId,
    ) {}
}
