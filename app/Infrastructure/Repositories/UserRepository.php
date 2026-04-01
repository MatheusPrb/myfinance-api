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

    public function create(User $user, string $hashedPassword): User
    {
        $model = new UserModel;

        $model->id = $user->id();
        $model->nome = $user->name();
        $model->email = $user->email();
        $model->senha = $hashedPassword;
        $model->save();

        return $this->toEntity($model);
    }

    private function toEntity(UserModel $model): User
    {
        return new User(
            $model->id,
            $model->nome,
            $model->email,
            $model->created_at?->toDateTimeImmutable(),
            $model->updated_at?->toDateTimeImmutable(),
        );
    }
}
