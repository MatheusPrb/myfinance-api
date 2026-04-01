<?php

namespace App\Application\UseCases\RegisterUser;

use App\Domain\Entities\User;

final readonly class RegisterUserOutput
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
