<?php

namespace App\Application\UseCases\SummarizeSpendingBySubcategory;

final readonly class SummarizeSpendingBySubcategoryOutput
{
    public function __construct(
        public string $total,
        public array $bySubcategory,
    ) {}

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'by_subcategory' => $this->bySubcategory,
        ];
    }
}
