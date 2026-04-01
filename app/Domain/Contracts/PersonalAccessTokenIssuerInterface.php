<?php

namespace App\Domain\Contracts;

interface PersonalAccessTokenIssuerInterface
{
    public function issueForUserId(string $userId): string;
}
