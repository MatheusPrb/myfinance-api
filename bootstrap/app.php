<?php

use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\ForbiddenNotAdminException;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Responses\ApiResponse;
use App\Messages\Messages;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => null);

        $trusted = env('TRUSTED_PROXIES');
        if (filled($trusted)) {
            $middleware->trustProxies(
                at: $trusted === '*' ? '*' : array_map(trim(...), explode(',', $trusted)),
            );
        }

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);
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

        $exceptions->render(function (DomainException|ForbiddenNotAdminException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                [],
                $e->getStatusCode()
            );
        });

        $exceptions->render(function (AuthenticationException $e) {
            return ApiResponse::error(
                $e->getMessage(),
                [],
                401
            );
        });

        $exceptions->render(function (NotFoundHttpException $e) {
            return ApiResponse::error(  
                Messages::NOT_FOUND,
                [],
                404
            );
        });

        $exceptions->render(function (\Throwable $e) {
            return ApiResponse::error(
                // $e->getMessage(),
                Messages::INTERNAL_SERVER_ERROR,
                [],
                500
            );
        });
    })->create();
