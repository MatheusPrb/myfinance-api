<?php

namespace Tests\Feature;

use App\Messages\Messages;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/expenses', [
            'category_id' => '00000000-0000-4000-8000-000000000001',
            'value' => 10,
        ]);

        $response->assertUnauthorized();
    }

    public function test_creates_expense_with_valid_payload(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::query()->create(['name' => 'Test Category']);
        $subcategory = Subcategory::query()->create([
            'category_id' => $category->id,
            'name' => 'Test Sub',
        ]);

        $response = $this->postJson('/api/v1/expenses', [
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'description' => 'Coffee',
            'value' => 12.5,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.category_id', $category->id)
            ->assertJsonPath('data.subcategory_id', $subcategory->id)
            ->assertJsonPath('data.description', 'Coffee');

        $this->assertDatabaseHas('expenses', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
        ]);
    }

    public function test_rejects_subcategory_from_another_category(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $categoryA = Category::query()->create(['name' => 'Category A']);
        $categoryB = Category::query()->create(['name' => 'Category B']);
        $subOfB = Subcategory::query()->create([
            'category_id' => $categoryB->id,
            'name' => 'Sub of B',
        ]);

        $response = $this->postJson('/api/v1/expenses', [
            'category_id' => $categoryA->id,
            'subcategory_id' => $subOfB->id,
            'value' => 1,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', Messages::SUBCATEGORY_DOES_NOT_BELONG_TO_CATEGORY);
    }

    public function test_rejects_negative_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::query()->create(['name' => 'Cat']);

        $response = $this->postJson('/api/v1/expenses', [
            'category_id' => $category->id,
            'value' => -1,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', Messages::INVALID_DATA);
    }
}
