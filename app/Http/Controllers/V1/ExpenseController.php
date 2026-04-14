<?php

namespace App\Http\Controllers\V1;

use App\Application\UseCases\CreateExpense\CreateExpenseInput;
use App\Application\UseCases\CreateExpense\CreateExpenseUseCase;
use App\Application\UseCases\GetExpenseById\GetExpenseByIdInput;
use App\Application\UseCases\GetExpenseById\GetExpenseByIdUseCase;
use App\Application\UseCases\ListExpenses\ListExpensesInput;
use App\Application\UseCases\ListExpenses\ListExpensesUseCase;
use App\Application\UseCases\SummarizeSpending\SummarizeSpendingInput;
use App\Application\UseCases\SummarizeSpending\SummarizeSpendingUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\ListExpensesRequest;
use App\Http\Requests\ShowExpenseRequest;
use App\Http\Requests\SummarizeSpendingRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    public function index(ListExpensesRequest $request, ListExpensesUseCase $listExpensesUseCase): JsonResponse
    {

        $input = new ListExpensesInput(
            $request->user()->id,
            $request->validated('page', 1),
            $request->validated('per_page', 15),
            $request->validated('date_from', null),
            $request->validated('date_to', null),
            $request->validated('category_id', null),
        );

        $output = $listExpensesUseCase->execute($input);

        return ApiResponse::success($output->toArray());
    }

    public function summary(SummarizeSpendingRequest $request, SummarizeSpendingUseCase $summarizeSpendingUseCase): JsonResponse
    {
        $input = new SummarizeSpendingInput(
            $request->user()->id,
            $request->validated('date_from') ?? null,
            $request->validated('date_to') ?? null,
        );

        $output = $summarizeSpendingUseCase->execute($input);

        return ApiResponse::success($output->toArray());
    }

    public function show(ShowExpenseRequest $request, GetExpenseByIdUseCase $getExpenseByIdUseCase): JsonResponse
    {
        $input = new GetExpenseByIdInput(
            $request->user()->id,
            $request->validated('id'),
        );

        $output = $getExpenseByIdUseCase->execute($input);

        return ApiResponse::success($output->toArray());
    }

    public function store(CreateExpenseRequest $request, CreateExpenseUseCase $createExpenseUseCase): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $input = CreateExpenseInput::fromArray($data);

        $output = $createExpenseUseCase->execute($input);

        return ApiResponse::success($output->toArray(), 201);
    }
}
