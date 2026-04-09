<?php

namespace App\Http\Controllers\V1;

use App\Application\UseCases\ListCategories\ListCategoriesUseCase;
use App\Application\UseCases\ListSubcategoriesByCategory\ListSubcategoriesByCategoryInput;
use App\Application\UseCases\ListSubcategoriesByCategory\ListSubcategoriesByCategoryUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListSubcategoriesByCategoryRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(ListCategoriesUseCase $listCategoriesUseCase): JsonResponse
    {
        return ApiResponse::success($listCategoriesUseCase->execute()->toArray());
    }

    public function subcategories(
        ListSubcategoriesByCategoryRequest $request,
        ListSubcategoriesByCategoryUseCase $listSubcategoriesByCategoryUseCase,
    ): JsonResponse {
        $input = new ListSubcategoriesByCategoryInput($request->validated('category_id'));
        $output = $listSubcategoriesByCategoryUseCase->execute($input);

        return ApiResponse::success($output->toArray());
    }
}
