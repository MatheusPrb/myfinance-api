<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense as ExpenseModel;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SummarizeSpendingBySubcategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/expenses/summary/by-subcategory');

        $response->assertUnauthorized();
    }

    public function test_returns_zero_and_empty_rows_when_no_expenses(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/expenses/summary/by-subcategory');

        $response->assertOk()
            ->assertJsonPath('data.total', '0.00')
            ->assertJsonPath('data.by_subcategory', []);
    }

    public function test_groups_only_expenses_with_subcategory(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($user);

        $catFood = Category::query()->create(['name' => 'Alimentação']);
        $subRest = Subcategory::query()->create([
            'category_id' => $catFood->id,
            'name' => 'Restaurante',
        ]);

        ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $catFood->id,
            'subcategory_id' => $subRest->id,
            'description' => 'A',
            'value' => 10,
        ]);
        ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $catFood->id,
            'subcategory_id' => null,
            'description' => 'B',
            'value' => 5,
        ]);
        ExpenseModel::query()->create([
            'user_id' => $other->id,
            'category_id' => $catFood->id,
            'subcategory_id' => $subRest->id,
            'description' => 'Outro',
            'value' => 999,
        ]);

        $response = $this->getJson('/api/v1/expenses/summary/by-subcategory');

        $response->assertOk()
            ->assertJsonPath('data.total', '10.00')
        ;

        $rows = $response->json('data.by_subcategory');
        $this->assertCount(1, $rows);

        $this->assertSame('Restaurante', $rows[0]['subcategory_name']);
        $this->assertSame('10.00', $rows[0]['total']);
        $this->assertSame($subRest->id, $rows[0]['subcategory_id']);
    }

    public function test_respects_date_range(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $cat = Category::query()->create(['name' => 'X']);
        $sub = Subcategory::query()->create(['category_id' => $cat->id, 'name' => 'S']);

        $inside = ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'subcategory_id' => $sub->id,
            'description' => 'Dentro',
            'value' => 100,
        ]);
        DB::table('expenses')->where('id', $inside->id)->update([
            'created_at' => '2026-04-10 12:00:00',
            'updated_at' => '2026-04-10 12:00:00',
        ]);

        $outside = ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'subcategory_id' => $sub->id,
            'description' => 'Fora',
            'value' => 50,
        ]);
        DB::table('expenses')->where('id', $outside->id)->update([
            'created_at' => '2026-03-01 12:00:00',
            'updated_at' => '2026-03-01 12:00:00',
        ]);

        $response = $this->getJson('/api/v1/expenses/summary/by-subcategory?date_from=2026-04-05&date_to=2026-04-15');

        $response->assertOk()
            ->assertJsonPath('data.total', '100.00');

        $rows = $response->json('data.by_subcategory');
        $this->assertCount(1, $rows);
        $this->assertSame('100.00', $rows[0]['total']);
    }

    public function test_rejects_inverted_date_range(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/expenses/summary/by-subcategory?date_from=2026-04-20&date_to=2026-04-10');

        $response->assertUnprocessable();
    }
}
