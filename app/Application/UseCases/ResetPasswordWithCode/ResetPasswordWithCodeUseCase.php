<?php

namespace App\Application\UseCases\ResetPasswordWithCode;

use App\Application\Support\PasswordResetCacheKeys;
use App\Domain\Contracts\CodeRepositoryInterface;
use App\Domain\Contracts\PasswordHasherInterface;
use App\Domain\Contracts\UserRepositoryInterface;
use App\Domain\Enums\CodeType;
use App\Domain\Exceptions\InvalidPasswordResetCodeException;
use App\Domain\Exceptions\PasswordResetTooManyAttemptsException;
use App\Messages\Messages;
use Illuminate\Support\Facades\Cache;

final class ResetPasswordWithCodeUseCase
{
    public function __construct(
        private UserRepositoryInterface $users,
        private PasswordHasherInterface $hasher,
        private CodeRepositoryInterface $codes,
    ) {}

    public function execute(ResetPasswordWithCodeInput $input): void
    {
        $email = $input->email;
        $otpKey = PasswordResetCacheKeys::otp($email);
        $failKey = PasswordResetCacheKeys::fail($email);

        $codeHash = Cache::get($otpKey);
        $isValidCode = is_string($codeHash)
            ? hash_equals($codeHash, hash('sha256', $input->code))
            : $this->codes->matchesPlainCode($email, CodeType::PasswordReset, $input->code)
        ;

        if (!$isValidCode) {
            $this->registerFailedAttempt($email, $otpKey, $failKey);
        }

        $user = $this->users->findByEmail($email);
        if (!$user) {
            Cache::forget($otpKey);
            Cache::forget($failKey);
            throw new InvalidPasswordResetCodeException(Messages::PASSWORD_RESET_CODE_INVALID);
        }

        $hashed = $this->hasher->hash($input->password);
        $this->users->updatePassword($user->id(), $hashed);
        $this->users->revokeAllPersonalAccessTokens($user->id());
        $this->codes->deleteByEmailAndType($email, CodeType::PasswordReset);

        Cache::forget($otpKey);
        Cache::forget($failKey);
    }

    private function registerFailedAttempt(string $email, string $otpKey, string $failKey): never
    {
        $maxAttempts = config('password_reset.max_attempts');
        $ttlMinutes = config('password_reset.ttl_minutes');

        $fails = Cache::get($failKey, 0);
        $fails++;
        Cache::put(
            $failKey,
            $fails,
            now()->addMinutes($ttlMinutes),
        );

        if ($fails >= $maxAttempts) {
            Cache::forget($otpKey);
            Cache::forget($failKey);
            $this->codes->deleteByEmailAndType($email, CodeType::PasswordReset);
            throw new PasswordResetTooManyAttemptsException(Messages::PASSWORD_RESET_TOO_MANY_ATTEMPTS);
        }

        throw new InvalidPasswordResetCodeException(Messages::PASSWORD_RESET_CODE_INVALID);
    }
}
