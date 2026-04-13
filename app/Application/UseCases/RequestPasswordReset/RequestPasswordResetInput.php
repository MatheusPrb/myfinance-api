<?php

namespace App\Application\UseCases\RequestPasswordReset;

final class RequestPasswordResetInput
{
    public function __construct(
        public readonly string $email,
    ) {}

    /**
     * @param  array{email: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(trim($data['email']));
    }
}
