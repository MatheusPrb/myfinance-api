<?php

namespace Tests\Feature;

use App\Messages\Messages;
use App\Models\Category;
use App\Models\Expense as ExpenseModel;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListAndGetExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/expenses');

        $response->assertUnauthorized();
    }

    public function test_lists_expenses_paginated_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::query()->create(['name' => 'Cat']);
        $sub = Subcategory::query()->create([
            'category_id' => $category->id,
            'name' => 'Sub',
        ]);

        ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subcategory_id' => $sub->id,
            'description' => 'Mine',
            'value' => 10,
        ]);
        ExpenseModel::query()->create([
            'user_id' => $other->id,
            'category_id' => $category->id,
            'subcategory_id' => null,
            'description' => 'Other user',
            'value' => 99,
        ]);

        $response = $this->getJson('/api/v1/expenses?per_page=1&page=1');

        $response->assertOk()
            ->assertJsonPath('data.meta.total', 1)
            ->assertJsonPath('data.meta.per_page', 1)
            ->assertJsonPath('data.meta.current_page', 1)
            ->assertJsonPath('data.items.0.description', 'Mine')
            ->assertJsonPath('data.items.0.category_name', 'Cat')
            ->assertJsonPath('data.items.0.subcategory_name', 'Sub')
        ;
    }

    public function test_list_filters_by_created_at_date_range(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $cat = Category::query()->create(['name' => 'A']);

        $inside = ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'subcategory_id' => null,
            'description' => 'Dentro',
            'value' => 10,
        ]);
        DB::table('expenses')->where('id', $inside->id)->update([
            'created_at' => '2026-04-15 12:00:00',
            'updated_at' => '2026-04-15 12:00:00',
        ]);

        $outside = ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'subcategory_id' => null,
            'description' => 'Fora',
            'value' => 99,
        ]);
        DB::table('expenses')->where('id', $outside->id)->update([
            'created_at' => '2026-03-01 12:00:00',
            'updated_at' => '2026-03-01 12:00:00',
        ]);

        $response = $this->getJson('/api/v1/expenses?date_from=2026-04-10&date_to=2026-04-20');

        $response->assertOk()
            ->assertJsonPath('data.meta.total', 1)
            ->assertJsonPath('data.items.0.description', 'Dentro')
        ;
    }

    public function test_list_filters_by_category_id(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $catA = Category::query()->create(['name' => 'A']);
        $catB = Category::query()->create(['name' => 'B']);

        ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $catA->id,
            'subcategory_id' => null,
            'description' => 'Only A',
            'value' => 1,
        ]);
        ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $catB->id,
            'subcategory_id' => null,
            'description' => 'Only B',
            'value' => 2,
        ]);

        $response = $this->getJson("/api/v1/expenses?category_id={$catA->id}");

        $response->assertOk()
            ->assertJsonPath('data.meta.total', 1)
            ->assertJsonPath('data.items.0.description', 'Only A');
    }

    public function test_list_accepts_categoria_id_query_alias(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $cat = Category::query()->create(['name' => 'Uber']);

        ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'subcategory_id' => null,
            'description' => 'Ride',
            'value' => 5,
        ]);

        $response = $this->getJson('/api/v1/expenses?'.http_build_query([
            'categoriaId' => $cat->id,
        ]));

        $response->assertOk()
            ->assertJsonPath('data.meta.total', 1)
            ->assertJsonPath('data.items.0.description', 'Ride');
    }

    public function test_list_rejects_date_from_without_date_to(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/expenses?date_from=2026-04-01');

        $response->assertUnprocessable();
    }

    public function test_show_returns_single_expense_for_owner(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::query()->create(['name' => 'Cat']);
        $expense = ExpenseModel::query()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subcategory_id' => null,
            'description' => 'Lunch',
            'value' => 25.5,
        ]);

        $response = $this->getJson("/api/v1/expenses/{$expense->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $expense->id)
            ->assertJsonPath('data.description', 'Lunch')
            ->assertJsonPath('data.category_name', 'Cat')
            ->assertJsonPath('data.subcategory_name', null)
        ;
    }

    public function test_show_rejects_non_uuid_v4_id(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/expenses/not-a-uuid');

        $response->assertStatus(422)
            ->assertJsonPath('message', Messages::INVALID_DATA)
        ;
    }

    public function test_show_rejects_uuid_that_is_not_version_4(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $uuidV1 = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

        $response = $this->getJson("/api/v1/expenses/{$uuidV1}");

        $response->assertStatus(422)
            ->assertJsonPath('message', Messages::INVALID_DATA)
        ;
    }

    public function test_show_returns_404_for_other_users_expense(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        Sanctum::actingAs($intruder);

        $category = Category::query()->create(['name' => 'Cat']);
        $expense = ExpenseModel::query()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'subcategory_id' => null,
            'description' => 'Secret',
            'value' => 1,
        ]);

        $response = $this->getJson("/api/v1/expenses/{$expense->id}");

        $response->assertNotFound()
            ->assertJsonPath('message', Messages::EXPENSE_NOT_FOUND)
        ;
    }
}
