<?php

namespace App\Application\UseCases\ListExpenses;

use App\Domain\Contracts\ExpenseRepositoryInterface;

final class ListExpensesUseCase
{
    public function __construct(
        private ExpenseRepositoryInterface $expenses,
    ) {}

    public function execute(ListExpensesInput $input): ListExpensesOutput
    {
        $page = $this->expenses->paginateByUserId($input);

        return new ListExpensesOutput(
            $page['items'],
            $page['total'],
            $page['per_page'],
            $page['current_page'],
            $page['last_page'],
            $page['next_page_url'],
            $page['prev_page_url'],
        );
    }
}
