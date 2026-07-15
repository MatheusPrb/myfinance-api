<?php

namespace App\Providers;

use App\Domain\Contracts\CategoryRepositoryInterface;
use App\Domain\Contracts\CodeRepositoryInterface;
use App\Domain\Contracts\ExpenseRepositoryInterface;
use App\Domain\Contracts\PasswordHasherInterface;
use App\Domain\Contracts\PersonalAccessTokenIssuerInterface;
use App\Domain\Contracts\SubcategoryRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Auth\SanctumPersonalAccessTokenIssuer;
use App\Infrastructure\Repositories\CategoryRepository;
use App\Infrastructure\Repositories\CodeRepository;
use App\Infrastructure\Repositories\ExpenseRepository;
use App\Infrastructure\Repositories\SubcategoryRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Security\PasswordHasher;
use App\Models\PersonalAccessToken;
use Illuminate\Cache\RateLimiting\Limit;
// use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SubcategoryRepositoryInterface::class, SubcategoryRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->bind(CodeRepositoryInterface::class, CodeRepository::class);
        $this->app->bind(PasswordHasherInterface::class, PasswordHasher::class);
        $this->app->bind(PersonalAccessTokenIssuerInterface::class, SanctumPersonalAccessTokenIssuer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // TODO(remover): quebra proposital do /up para testar o rollback automatico do deploy.sh
        // Event::listen(DiagnosingHealth::class, function (): void {
        //     throw new \RuntimeException('Rollback test: healthcheck quebrado de proposito');
        // });

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        RateLimiter::for('auth-login', function (Request $request) {
            $perMinute = app()->environment('testing', 'local') ? 1000 : 5;
            $email = (string) $request->input('email', '');

            return Limit::perMinute($perMinute)->by($request->ip().':'.$email);
        });

        RateLimiter::for('auth-register', function (Request $request) {
            $perMinute = app()->environment('testing', 'local') ? 1000 : 3;

            return Limit::perMinute($perMinute)->by($request->ip());
        });

        RateLimiter::for('auth-password-email', function (Request $request) {
            $perMinute = app()->environment('testing', 'local') ? 1000 : 3;
            $email = (string) $request->input('email', '');

            return Limit::perMinute($perMinute)->by($request->ip().':'.$email);
        });

        RateLimiter::for('private-api', function (Request $request) {
            $perMinute = 30;
            return Limit::perMinute($perMinute)->by($request->user()->id . ':' . $request->ip());
        });

        RateLimiter::for('admin-logs', function (Request $request) {
            $perMinute = app()->environment('testing', 'local') ? 1000 : 60;
            $userId = (string) ($request->user()?->id ?? $request->ip());

            return Limit::perMinute($perMinute)->by($userId.':'.$request->ip());
        });
    }
}
