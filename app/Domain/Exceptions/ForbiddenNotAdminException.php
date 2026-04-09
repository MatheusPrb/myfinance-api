<?php

namespace App\Domain\Exceptions;

use App\Messages\Messages;

final class ForbiddenNotAdminException extends DomainException
{
    protected int $statusCode = 403;

    public function __construct()
    {
        parent::__construct(Messages::FORBIDDEN_NOT_ADMIN);
    }
}
