<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/v1/logout')->assertUnauthorized();
    }

    public function test_logout_invalidates_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->assertSame(1, $user->tokens()->count());

        $this->withToken($token)
            ->postJson('/api/v1/logout')
            ->assertOk()
            ->assertJson(['data' => null])
        ;

        $this->assertSame(0, $user->fresh()->tokens()->count());
    }
}
