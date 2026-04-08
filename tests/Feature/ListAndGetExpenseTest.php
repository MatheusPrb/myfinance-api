<?php

namespace Tests\Feature;

use App\Messages\Messages;
use App\Models\Category;
use App\Models\Expense as ExpenseModel;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        ;
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
