<?php

namespace App\Domain\Exceptions;

final class InvalidSubcategoryForCategoryException extends DomainException
{
    protected int $statusCode = 422;
}
