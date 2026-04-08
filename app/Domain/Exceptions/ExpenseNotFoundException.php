<?php

namespace App\Domain\Exceptions;

final class ExpenseNotFoundException extends DomainException
{
    protected int $statusCode = 404;
}
