<?php

use App\Http\Controllers\V1\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/register', [AuthenticationController::class, 'register']);
Route::post('/v1/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
