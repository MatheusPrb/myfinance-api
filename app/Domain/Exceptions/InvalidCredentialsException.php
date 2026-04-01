<?php

namespace App\Domain\Exceptions;

final class InvalidCredentialsException extends DomainException
{
    protected int $statusCode = 401;
}
