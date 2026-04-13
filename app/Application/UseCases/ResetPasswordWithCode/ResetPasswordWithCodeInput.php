<?php

namespace App\Application\UseCases\ResetPasswordWithCode;

final class ResetPasswordWithCodeInput
{
    public function __construct(
        public readonly string $email,
        public readonly string $code,
        public readonly string $password,
    ) {}

    /**
     * @param  array{email: string, code: string, password: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            trim($data['email']),
            trim($data['code']),
            $data['password'],
        );
    }
}
