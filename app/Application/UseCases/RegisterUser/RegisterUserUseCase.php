<?php

namespace App\Application\UseCases\RegisterUser;

use App\Domain\Contracts\PasswordHasherInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Helper\Uuid;
use App\Domain\Exceptions\EmailAlreadyRegisteredException;
use App\Messages\Messages;

final class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $hasher,
    ) {}

    public function execute(RegisterUserInput $input): RegisterUserOutput
    {
        if ($this->users->existsByEmail($input->email)) {
            throw new EmailAlreadyRegisteredException(Messages::EMAIL_ALREADY_REGISTERED);
        }

        $hashed = $this->hasher->hash($input->password);
        $id = Uuid::generate();

        $user = new User(
            $id,
            $input->name,
            $input->email
        );

        return new RegisterUserOutput(
            $this->users->create($user, $hashed)
        );
    }
}
