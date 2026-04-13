<?php

namespace Tests\Feature;

use App\Messages\Messages;
use App\Models\ApplicationLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminLogsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_admin_logs_requires_authentication(): void
    {
        $this->getJson('/api/v1/admin/logs')->assertUnauthorized();
    }

    public function test_list_admin_logs_returns_403_for_non_admin(): void
    {
        Sanctum::actingAs(User::factory()->create(['is_admin' => false]));

        $this->getJson('/api/v1/admin/logs')
            ->assertForbidden()
            ->assertJsonPath('message', Messages::FORBIDDEN_NOT_ADMIN)
        ;
    }

    public function test_list_admin_logs_returns_200_with_items_and_meta_for_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $subject = User::factory()->create(['name' => 'Log Subject']);

        ApplicationLog::query()->create([
            'channel' => 'laravel',
            'level' => 'error',
            'message' => 'Something failed',
            'context' => [
                'userId' => $subject->id,
                'subject_type' => 'expense',
                'subject_id' => '00000000-0000-4000-8000-000000000001',
            ],
            'extra' => ['ip' => '10.0.0.1', 'user_agent' => 'PHPUnit'],
            'created_at' => now()->subMinute(),
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/logs?page=1&per_page=25');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'items' => [
                        [
                            'id',
                            'user_id',
                            'user_name',
                            'action',
                            'subject_type',
                            'subject_id',
                            'description',
                            'properties',
                            'ip_address',
                            'user_agent',
                            'created_at',
                        ],
                    ],
                    'meta' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                        'next_page_url',
                        'prev_page_url',
                    ],
                ],
            ]);

        $items = $response->json('data.items');
        $this->assertCount(1, $items);
        $this->assertSame($subject->id, $items[0]['user_id']);
        $this->assertSame('Log Subject', $items[0]['user_name']);
        $this->assertSame('error:laravel', $items[0]['action']);
        $this->assertSame('expense', $items[0]['subject_type']);
        $this->assertSame('00000000-0000-4000-8000-000000000001', $items[0]['subject_id']);
        $this->assertSame('Something failed', $items[0]['description']);
        $this->assertSame('10.0.0.1', $items[0]['ip_address']);
        $this->assertSame('PHPUnit', $items[0]['user_agent']);

        $meta = $response->json('data.meta');
        $this->assertSame(1, $meta['current_page']);
        $this->assertSame(25, $meta['per_page']);
        $this->assertSame(1, $meta['total']);
        $this->assertSame(1, $meta['last_page']);
    }

    public function test_list_admin_logs_rejects_per_page_above_max(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->getJson('/api/v1/admin/logs?per_page=999')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page'])
        ;
    }
}
