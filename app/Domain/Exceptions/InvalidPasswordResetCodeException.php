<?php

namespace App\Domain\Exceptions;

final class InvalidPasswordResetCodeException extends DomainException
{
    protected int $statusCode = 400;
}
