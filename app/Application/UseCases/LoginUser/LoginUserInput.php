<?php

namespace App\Application\UseCases\LoginUser;

final readonly class LoginUserInput
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}

    /**
     * @param  array{email: string, password: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data['email'], $data['password']);
    }
}
