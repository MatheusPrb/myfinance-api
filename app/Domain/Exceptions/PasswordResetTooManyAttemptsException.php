<?php

namespace App\Domain\Exceptions;

final class PasswordResetTooManyAttemptsException extends DomainException
{
    protected int $statusCode = 400;
}
