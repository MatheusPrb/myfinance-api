<?php

namespace App\Domain\Contracts;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function existsByEmail(string $email): bool;
    public function create(User $user, string $hashedPassword): User;
}
