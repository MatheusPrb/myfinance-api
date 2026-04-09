<?php

namespace App\Domain\Exceptions;

final class CategoryNotFoundException extends DomainException
{
    protected int $statusCode = 404;
}
