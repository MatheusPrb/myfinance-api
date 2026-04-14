<?php

namespace App\Application\UseCases\SummarizeSpendingBySubcategory;

use App\Domain\Contracts\ExpenseRepositoryInterface;

final class SummarizeSpendingBySubcategoryUseCase
{
    public function __construct(
        private ExpenseRepositoryInterface $expenses,
    ) {}

    public function execute(SummarizeSpendingBySubcategoryInput $input): SummarizeSpendingBySubcategoryOutput
    {
        $summary = $this->expenses->spendingSummaryBySubcategoryByUserId($input);

        return new SummarizeSpendingBySubcategoryOutput(
            $summary['total'],
            $summary['by_subcategory'],
        );
    }
}
