<?php

namespace App\Application\UseCases\SummarizeSpendingBySubcategory;

final readonly class SummarizeSpendingBySubcategoryInput
{
    public function __construct(
        public readonly string $userId,
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['user_id'],
            $data['date_from'] ?? null,
            $data['date_to'] ?? null,
        );
    }
}
