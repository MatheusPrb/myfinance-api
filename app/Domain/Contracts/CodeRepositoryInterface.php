<?php

namespace App\Domain\Contracts;

use App\Domain\Enums\CodeType;

interface CodeRepositoryInterface
{
    public function replacePlainCode(string $email, CodeType $type, string $plainCode): void;
    public function deleteByEmailAndType(string $email, CodeType $type): void;
}
