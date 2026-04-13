<?php

namespace App\Application\Support;

final class PasswordResetCacheKeys
{
    public static function otp(string $email): string
    {
        return 'email_pwd_reset_code:'.hash('sha256', strtolower($email));
    }

    public static function fail(string $email): string
    {
        return 'email_pwd_reset_fail:'.hash('sha256', strtolower($email));
    }

    private function __construct() {}
}
