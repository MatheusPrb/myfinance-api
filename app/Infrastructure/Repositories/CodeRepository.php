<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\CodeRepositoryInterface;
use App\Domain\Enums\CodeType;
use App\Models\Code as CodeModel;

final class CodeRepository implements CodeRepositoryInterface
{
    public function replacePlainCode(string $email, CodeType $type, string $plainCode): void
    {
        $this->deleteByEmailAndType($email, $type);

        CodeModel::query()->create([
            'type' => $type->value,
            'email' => $email,
            'code' => $plainCode,
            'created_at' => now(),
        ]);
    }

    public function deleteByEmailAndType(string $email, CodeType $type): void
    {
        CodeModel::query()
            ->where('email', $email)
            ->where('type', $type->value)
            ->delete()
        ;
    }
}
