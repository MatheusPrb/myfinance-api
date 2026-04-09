<?php

namespace Tests\Feature;

use App\Messages\Messages;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_categories_requires_authentication(): void
    {
        $this->getJson('/api/v1/categories')->assertUnauthorized();
    }

    public function test_list_categories_returns_id_and_name_ordered_by_name(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Category::query()->create(['name' => 'Zebra']);
        Category::query()->create(['name' => 'Alpha']);

        $response = $this->getJson('/api/v1/categories');

        $response->assertOk();
        $items = $response->json('data.items');
        $this->assertCount(2, $items);
        $this->assertSame('Alpha', $items[0]['name']);
        $this->assertSame('Zebra', $items[1]['name']);
        $this->assertArrayHasKey('id', $items[0]);
    }

    public function test_list_subcategories_requires_authentication(): void
    {
        $cat = Category::query()->create(['name' => 'C']);

        $this->getJson("/api/v1/categories/{$cat->id}/subcategories")
            ->assertUnauthorized();
    }

    public function test_list_subcategories_returns_subcategories_for_category(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $cat = Category::query()->create(['name' => 'Food']);
        Subcategory::query()->create(['category_id' => $cat->id, 'name' => 'B']);
        Subcategory::query()->create(['category_id' => $cat->id, 'name' => 'A']);

        $response = $this->getJson("/api/v1/categories/{$cat->id}/subcategories");

        $response->assertOk();
        $items = $response->json('data.items');
        $this->assertCount(2, $items);
        $this->assertSame('A', $items[0]['name']);
        $this->assertSame('B', $items[1]['name']);
    }

    public function test_list_subcategories_returns_404_when_category_missing(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $missingId = '00000000-0000-4000-8000-000000000099';

        $response = $this->getJson("/api/v1/categories/{$missingId}/subcategories");

        $response->assertNotFound()
            ->assertJsonPath('message', Messages::CATEGORY_NOT_FOUND);
    }
}
