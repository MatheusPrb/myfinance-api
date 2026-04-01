<?php

namespace App\Infrastructure\Security;

use App\Domain\Contracts\PasswordHasherInterface;
use Illuminate\Contracts\Hashing\Hasher;

final class PasswordHasher implements PasswordHasherInterface
{
    public function __construct(private Hasher $hasher) {}

    public function hash(string $plain): string
    {
        return $this->hasher->make($plain);
    }

    public function verify(string $plain, string $hashed): bool
    {
        return $this->hasher->check($plain, $hashed);
    }
}
