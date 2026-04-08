<?php

namespace App\Application\UseCases\SummarizeSpending;

final readonly class SummarizeSpendingOutput
{
    /**
     * @param  list<array{category_id: string, category_name: string, total: string}>  $byCategory
     */
    public function __construct(
        public string $total,
        public array $byCategory,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'by_category' => $this->byCategory,
        ];
    }
}
