<?php

namespace App\Providers;

use App\Domain\Contracts\PasswordHasherInterface;
use App\Domain\Contracts\PersonalAccessTokenIssuerInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Infrastructure\Auth\SanctumPersonalAccessTokenIssuer;
use App\Infrastructure\Repositories\UserRepository;
use App\Infrastructure\Security\PasswordHasher;
use App\Models\PersonalAccessToken;
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
        $this->app->bind(PasswordHasherInterface::class, PasswordHasher::class);
        $this->app->bind(PersonalAccessTokenIssuerInterface::class, SanctumPersonalAccessTokenIssuer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
