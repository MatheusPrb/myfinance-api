<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Models\User as UserModel;

final class UserRepository implements UserRepositoryInterface
{
    public function existsByEmail(string $email): bool
    {
        return UserModel::query()->where('email', $email)->exists();
    }

    public function findWithPasswordHashByEmail(string $email): ?array
    {
        $model = UserModel::query()->where('email', $email)->first();

        if ($model === null) {
            return null;
        }

        return [
            'user' => $this->toEntity($model),
            'passwordHash' => (string) $model->getRawOriginal('password'),
        ];
    }

    public function create(User $user, string $hashedPassword): User
    {
        $model = new UserModel;

        $model->id = $user->id();
        $model->name = $user->name();
        $model->email = $user->email();
        $model->password = $hashedPassword;
        $model->save();

        return $this->toEntity($model);
    }

    public function findByEmail(string $email): ?User
    {
        $model = UserModel::query()->where('email', $email)->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function updatePassword(string $userId, string $hashedPassword): void
    {
        $model = UserModel::query()->whereKey($userId)->first();

        if ($model === null) {
            return;
        }

        $model->password = $hashedPassword;
        $model->save();
    }

    public function revokeAllPersonalAccessTokens(string $userId): void
    {
        $model = UserModel::query()->whereKey($userId)->first();

        $model?->tokens()->delete();
    }

    private function toEntity(UserModel $model): User
    {
        return new User(
            $model->id,
            $model->name,
            $model->email,
            $model->created_at?->toDateTimeImmutable(),
            $model->updated_at?->toDateTimeImmutable(),
        );
    }
}
