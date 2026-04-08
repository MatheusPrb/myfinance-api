<?php

namespace App\Http\Controllers\V1;

use App\Application\UseCases\LoginUser\LoginUserInput;
use App\Application\UseCases\LoginUser\LoginUserUseCase;
use App\Application\UseCases\RegisterUser\RegisterUserInput;
use App\Application\UseCases\RegisterUser\RegisterUserUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Responses\ApiResponse;

class AuthenticationController extends Controller
{
    public function register(RegisterUserRequest $request, RegisterUserUseCase $registerUserUseCase)
    {
        $input = RegisterUserInput::fromArray($request->validated());

        $registerUserUseCase->execute($input);

        return ApiResponse::success(null, 201);
    }

    public function login(LoginUserRequest $request, LoginUserUseCase $loginUserUseCase)
    {
        $input = LoginUserInput::fromArray($request->validated());

        $output = $loginUserUseCase->execute($input);

        return ApiResponse::success($output->token);
    }
}
