<?php

namespace App\Infrastructure\Auth;

use App\Domain\Contracts\PersonalAccessTokenIssuerInterface;
use App\Models\User as UserModel;
use RuntimeException;

final class SanctumPersonalAccessTokenIssuer implements PersonalAccessTokenIssuerInterface
{
    public function issueForUserId(string $userId): string
    {
        $user = UserModel::query()->find($userId);

        if ($user === null) {
            throw new RuntimeException('User not found for token issuance.');
        }

        return $user->createToken('auth')->plainTextToken;
    }
}
