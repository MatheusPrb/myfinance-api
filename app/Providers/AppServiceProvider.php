<?php

namespace App\Providers;

use App\Domain\Contracts\CategoryRepositoryInterface;
use App\Domain\Contracts\ExpenseRepositoryInterface;
use App\Domain\Contracts\PasswordHasherInterface;
use App\Domain\Contracts\PersonalAccessTokenIssuerInterface;
use App\Domain\Contracts\SubcategoryRepositoryInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Auth\SanctumPersonalAccessTokenIssuer;
use App\Infrastructure\Repositories\CategoryRepository;
use App\Infrastructure\Repositories\ExpenseRepository;
use App\Infrastructure\Repositories\SubcategoryRepository;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Security\PasswordHasher;
use App\Models\PersonalAccessToken;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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
        $this->app->bind(PasswordHasherInterface::class, PasswordHasher::class);
        $this->app->bind(PersonalAccessTokenIssuerInterface::class, SanctumPersonalAccessTokenIssuer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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

        RateLimiter::for('private-api', function (Request $request) {
            $perMinute = 30;
            return Limit::perMinute($perMinute)->by($request->user()->id . ':' . $request->ip());
        });
    }
}
