<?php

namespace App\Http\Controllers\V1;

use App\Application\UseCases\LoginUser\LoginUserInput;
use App\Application\UseCases\LoginUser\LoginUserUseCase;
use App\Application\UseCases\RegisterUser\RegisterUserInput;
use App\Application\UseCases\RegisterUser\RegisterUserUseCase;
use App\Application\UseCases\RequestPasswordReset\RequestPasswordResetInput;
use App\Application\UseCases\RequestPasswordReset\RequestPasswordResetUseCase;
use App\Application\UseCases\ResetPasswordWithCode\ResetPasswordWithCodeInput;
use App\Application\UseCases\ResetPasswordWithCode\ResetPasswordWithCodeUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\RequestPasswordResetRequest;
use App\Http\Requests\ResetPasswordWithCodeRequest;
use App\Http\Responses\ApiResponse;
use App\Messages\Messages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return ApiResponse::success();
    }

    public function requestPasswordReset(RequestPasswordResetRequest $request, RequestPasswordResetUseCase $useCase): JsonResponse
    {
        $input = RequestPasswordResetInput::fromArray($request->validated());
        $useCase->execute($input);

        return ApiResponse::success([
            'message' => Messages::PASSWORD_RESET_EMAIL_SENT,
        ]);
    }

    public function resetPasswordWithCode(ResetPasswordWithCodeRequest $request, ResetPasswordWithCodeUseCase $useCase): JsonResponse
    {
        $input = ResetPasswordWithCodeInput::fromArray($request->validated());
        $useCase->execute($input);

        return ApiResponse::success(null);
    }
}
