<?php

namespace App\Http\Controllers\V1;

use App\Application\UseCases\CreateExpense\CreateExpenseInput;
use App\Application\UseCases\CreateExpense\CreateExpenseUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExpenseRequest;
use App\Http\Responses\ApiResponse;
use App\Messages\Messages;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    public function store(CreateExpenseRequest $request, CreateExpenseUseCase $createExpenseUseCase): JsonResponse
    {
        $input = CreateExpenseInput::fromArray($request->validated());
        $output = $createExpenseUseCase->execute($input);

        return ApiResponse::success(
            $output->toArray(),
            Messages::EXPENSE_CREATED,
            201
        );
    }
}
