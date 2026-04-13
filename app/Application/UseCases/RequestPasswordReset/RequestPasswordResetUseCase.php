<?php

namespace App\Application\UseCases\RequestPasswordReset;

use App\Application\Support\PasswordResetCacheKeys;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Mail\PasswordResetCodeMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

final class RequestPasswordResetUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function execute(RequestPasswordResetInput $input): void
    {
        $email = $input->email;

        if (!$this->users->existsByEmail($email)) {
            return;
        }

        $code = sprintf('%06d', random_int(0, 999999));
        $ttlMinutes = config('password_reset.ttl_minutes');

        Cache::put(
            PasswordResetCacheKeys::otp($email),
            hash('sha256', $code),
            now()->addMinutes($ttlMinutes),
        );

        Mail::to($email)->send(new PasswordResetCodeMail($code, $ttlMinutes));
    }
}
