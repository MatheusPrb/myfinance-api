<?php

namespace App\Domain\Exceptions;

use Exception;

abstract class DomainException extends Exception
{
    protected int $statusCode = 400;

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function report(): bool
    {
        return false;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
