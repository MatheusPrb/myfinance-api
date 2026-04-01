<?php

namespace App\Application\UseCases\LoginUser;

use App\Domain\Entities\User;

final readonly class LoginUserOutput
{
    public function __construct(
        public string $token,
        public User $user,
    ) {}
}
