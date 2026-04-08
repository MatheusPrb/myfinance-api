<?php

namespace App\Application\UseCases\SummarizeSpending;

use App\Domain\Contracts\ExpenseRepositoryInterface;

final class SummarizeSpendingUseCase
{
    public function __construct(
        private ExpenseRepositoryInterface $expenses,
    ) {}

    public function execute(SummarizeSpendingInput $input): SummarizeSpendingOutput
    {
        $summary = $this->expenses->spendingSummaryByUserId($input->userId);

        return new SummarizeSpendingOutput(
            $summary['total'],
            $summary['by_category'],
        );
    }
}
