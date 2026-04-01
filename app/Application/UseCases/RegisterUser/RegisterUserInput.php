<?php

namespace App\Application\UseCases\RegisterUser;

final readonly class RegisterUserInput
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}

    /**
     * @param  array{name: string, email: string, password: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data['nome'], $data['email'], $data['password']);
    }
}
