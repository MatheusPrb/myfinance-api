<?php

namespace App\Helper;

use Illuminate\Support\Str;

final class Uuid
{
    public static function generate(): string
    {
        return (string) Str::uuid();
    }
}
