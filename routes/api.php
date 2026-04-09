<?php

use App\Http\Controllers\V1\AuthenticationController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\ExpenseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('register', [AuthenticationController::class, 'register']);
    Route::post('login', [AuthenticationController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('v1')->group(function (): void {
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{category}/subcategories', [CategoryController::class, 'subcategories']);
        Route::get('expenses/summary', [ExpenseController::class, 'summary']);
        Route::get('expenses', [ExpenseController::class, 'index']);
        Route::get('expenses/{id}', [ExpenseController::class, 'show']);
        Route::post('expenses', [ExpenseController::class, 'store']);
    });
});
