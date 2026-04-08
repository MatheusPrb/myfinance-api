<?php

namespace App\Application\UseCases\ListExpenses;

use App\Application\Presenters\ExpensePresenter;
use App\Domain\Entities\Expense;

final readonly class ListExpensesOutput
{
    /**
     * @param  list<Expense>  $items
     */
    public function __construct(
        public array $items,
        public int $total,
        public int $perPage,
        public int $currentPage,
        public int $lastPage,
        public ?string $nextPageUrl,
        public ?string $prevPageUrl,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'items' => array_map(
                static fn (Expense $expense): array => ExpensePresenter::toArray($expense),
                $this->items
            ),
            'meta' => [
                'current_page' => $this->currentPage,
                'per_page' => $this->perPage,
                'total' => $this->total,
                'last_page' => $this->lastPage,
                'next_page_url' => $this->nextPageUrl,
                'prev_page_url' => $this->prevPageUrl,
            ],
        ];
    }
}
