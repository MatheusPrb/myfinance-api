<?php

namespace App\Domain\Exceptions;

final class EmailAlreadyRegisteredException extends DomainException 
{
    protected int $statusCode = 409;
}
