<?php

namespace App\Http\Controllers\V1;

use App\Application\UseCases\RegisterUser\RegisterUserInput;
use App\Application\UseCases\RegisterUser\RegisterUserUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Responses\ApiResponse;

class AuthenticationController extends Controller
{
    public function register(RegisterUserRequest $request, RegisterUserUseCase $registerUserUseCase)
    {
        $input = RegisterUserInput::fromArray($request->validated());

        $output = $registerUserUseCase->execute($input);

        return ApiResponse::success(
            $output,
            null,
            201
        );
    }
}
