<?php

use App\Domain\Exceptions\DomainException;
use App\Http\Responses\ApiResponse;
use App\Messages\Messages;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (ValidationException $e) {
            return ApiResponse::error(
                Messages::INVALID_DATA,
                $e->errors(),
                422
            );
        });

        $exceptions->render(function (DomainException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                [],
                $e->getStatusCode()
            );
        });
    })->create();
