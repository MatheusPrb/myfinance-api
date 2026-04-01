<?php

namespace App\Application\UseCases\LoginUser;

use App\Domain\Contracts\PasswordHasherInterface;
use App\Domain\Contracts\PersonalAccessTokenIssuerInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Exceptions\InvalidCredentialsException;
use App\Messages\Messages;

final class LoginUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $hasher,
        private PersonalAccessTokenIssuerInterface $tokenIssuer,
    ) {}

    public function execute(LoginUserInput $input): LoginUserOutput
    {
        $row = $this->users->findWithPasswordHashByEmail($input->email);

        if ($row === null || ! $this->hasher->verify($input->password, $row['passwordHash'])) {
            throw new InvalidCredentialsException(Messages::INVALID_CREDENTIALS);
        }

        $token = $this->tokenIssuer->issueForUserId($row['user']->id());

        return new LoginUserOutput($token, $row['user']);
    }
}
