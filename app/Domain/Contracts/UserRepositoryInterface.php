<?php

namespace App\Domain\Contracts;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function existsByEmail(string $email): bool;
    public function findWithPasswordHashByEmail(string $email): ?array;
    public function create(User $user, string $hashedPassword): User;
    public function findByEmail(string $email): ?User;
    public function updatePassword(string $userId, string $hashedPassword): void;
    public function revokeAllPersonalAccessTokens(string $userId): void;
}
