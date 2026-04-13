<?php

namespace Tests\Feature;

use App\Application\Support\PasswordResetCacheKeys;
use App\Mail\PasswordResetCodeMail;
use App\Messages\Messages;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_mail_once_for_existing_user_and_identical_json_for_unknown_email(): void
    {
        $existing = User::factory()->create(['email' => 'member@example.com']);

        Mail::fake();

        $knownResponse = $this->postJson('/api/v1/password/forgot', [
            'email' => $existing->email,
        ]);

        Mail::assertSent(PasswordResetCodeMail::class);

        Mail::fake();

        $unknownResponse = $this->postJson('/api/v1/password/forgot', [
            'email' => 'ghost@example.com',
        ]);

        Mail::assertNothingOutgoing();

        $payload = [
            'data' => [
                'message' => Messages::PASSWORD_RESET_EMAIL_SENT,
            ],
        ];

        $knownResponse->assertOk()->assertJson($payload);
        $unknownResponse->assertOk()->assertJson($payload);
    }

    public function test_reset_with_valid_code_updates_password_and_revokes_tokens(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => 'OldPass1x',
        ]);
        $token = $user->createToken('device')->plainTextToken;
        $this->assertSame(1, $user->tokens()->count());

        Mail::fake();
        $this->postJson('/api/v1/password/forgot', ['email' => $user->email])->assertOk();

        $sent = Mail::sent(PasswordResetCodeMail::class);
        $this->assertCount(1, $sent);
        $code = $sent[0]->code;

        $this->postJson('/api/v1/password/reset', [
            'email' => $user->email,
            'code' => $code,
            'password' => 'NewPass2y',
        ])->assertOk()->assertJson(['data' => null]);

        $user->refresh();
        $this->assertTrue(Hash::check('NewPass2y', (string) $user->getRawOriginal('password')));
        $this->assertSame(0, $user->tokens()->count());

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'NewPass2y',
        ])->assertOk();

        $this->withToken($token)->postJson('/api/v1/logout')->assertUnauthorized();
    }

    public function test_reset_with_wrong_code_returns_domain_error(): void
    {
        $user = User::factory()->create(['email' => 'wrong@example.com']);

        Mail::fake();
        $this->postJson('/api/v1/password/forgot', ['email' => $user->email])->assertOk();

        $this->postJson('/api/v1/password/reset', [
            'email' => $user->email,
            'code' => '000000',
            'password' => 'NewPass2y',
        ])
            ->assertStatus(400)
            ->assertJson([
                'message' => Messages::PASSWORD_RESET_CODE_INVALID,
                'errors' => [],
            ])
        ;
    }

    public function test_lockout_after_max_failed_attempts_clears_code_and_blocks_prior_code(): void
    {
        config(['password_reset.max_attempts' => 5]);

        $user = User::factory()->create(['email' => 'lock@example.com']);

        Mail::fake();
        $this->postJson('/api/v1/password/forgot', ['email' => $user->email])->assertOk();

        $sent = Mail::sent(PasswordResetCodeMail::class);
        $validCode = $sent[0]->code;

        for ($i = 0; $i < 4; $i++) {
            $this->postJson('/api/v1/password/reset', [
                'email' => $user->email,
                'code' => '111111',
                'password' => 'NewPass2y',
            ])
                ->assertStatus(400)
                ->assertJson(['message' => Messages::PASSWORD_RESET_CODE_INVALID])
            ;
        }

        $this->postJson('/api/v1/password/reset', [
            'email' => $user->email,
            'code' => '111111',
            'password' => 'NewPass2y',
        ])
            ->assertStatus(400)
            ->assertJson(['message' => Messages::PASSWORD_RESET_TOO_MANY_ATTEMPTS])
        ;

        $this->assertFalse(Cache::has(PasswordResetCacheKeys::otp($user->email)));
        $this->assertFalse(Cache::has(PasswordResetCacheKeys::fail($user->email)));

        $this->postJson('/api/v1/password/reset', [
            'email' => $user->email,
            'code' => $validCode,
            'password' => 'NewPass2y',
        ])
            ->assertStatus(400)
            ->assertJson(['message' => Messages::PASSWORD_RESET_CODE_INVALID])
        ;
    }
}
