<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense as ExpenseModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SummarizeSpendingTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/expenses/summary');

        $response->assertUnauthorized();
    }

    public function test_returns_zero_totals_when_user_has_no_expenses(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/expenses/summary');

        $response->assertOk()
            ->assertJsonPath('data.total', '0.00')
            ->assertJsonPath('data.by_category', [])
        ;
    }

    public function test_aggregates_total_and_by_category_for_authenticated_user_only(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($user);

        $catFood = Category::query()->create(['name' => 'Alimentação']);
        $catUber = Category::query()->create(['name' => 'Mobilidade']);

        ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $catFood->id,
            'subcategory_id' => null,
            'description' => 'A',
            'value' => 10.5,
        ]);
        ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $catFood->id,
            'subcategory_id' => null,
            'description' => 'B',
            'value' => 20,
        ]);
        ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $catUber->id,
            'subcategory_id' => null,
            'description' => 'C',
            'value' => 15,
        ]);
        ExpenseModel::query()->create([
            'user_id' => $other->id,
            'category_id' => $catFood->id,
            'subcategory_id' => null,
            'description' => 'Outro usuário',
            'value' => 999,
        ]);

        $response = $this->getJson('/api/v1/expenses/summary');

        $response->assertOk()
            ->assertJsonPath('data.total', '45.50');

        $byCategory = $response->json('data.by_category');
        $this->assertCount(2, $byCategory);

        $this->assertSame('Alimentação', $byCategory[0]['category_name']);
        $this->assertSame('30.50', $byCategory[0]['total']);
        $this->assertSame($catFood->id, $byCategory[0]['category_id']);

        $this->assertSame('Mobilidade', $byCategory[1]['category_name']);
        $this->assertSame('15.00', $byCategory[1]['total']);
    }

    public function test_filters_summary_by_created_at_date_range(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::query()->create(['name' => 'Teste']);

        $inside = ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subcategory_id' => null,
            'description' => 'Dentro',
            'value' => 100,
        ]);
        DB::table('expenses')->where('id', $inside->id)->update([
            'created_at' => '2026-04-10 12:00:00',
            'updated_at' => '2026-04-10 12:00:00',
        ]);

        $outside = ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subcategory_id' => null,
            'description' => 'Fora (antes)',
            'value' => 50,
        ]);
        DB::table('expenses')->where('id', $outside->id)->update([
            'created_at' => '2026-04-01 12:00:00',
            'updated_at' => '2026-04-01 12:00:00',
        ]);

        $response = $this->getJson('/api/v1/expenses/summary?date_from=2026-04-05&date_to=2026-04-15');

        $response->assertOk()
            ->assertJsonPath('data.total', '100.00');

        $byCategory = $response->json('data.by_category');
        $this->assertCount(1, $byCategory);
        $this->assertSame('100.00', $byCategory[0]['total']);
    }

    public function test_rejects_inverted_date_range(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/expenses/summary?date_from=2026-04-12&date_to=2026-04-05');

        $response->assertUnprocessable();
    }

    public function test_rejects_date_from_without_date_to(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/expenses/summary?date_from=2026-04-01');

        $response->assertUnprocessable();
    }
}
