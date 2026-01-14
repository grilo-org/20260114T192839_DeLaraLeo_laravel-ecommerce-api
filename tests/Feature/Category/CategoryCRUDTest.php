<?php

namespace Tests\Feature\Category;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryCRUDTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_user_can_list_categories(): void
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_user_can_view_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'slug',
                'description',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'id' => $category->id,
                'name' => $category->name,
            ]);
    }

    public function test_authenticated_user_can_create_category(): void
    {
        Sanctum::actingAs($this->user);

        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test description',
        ];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'slug',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

    public function test_authenticated_user_can_create_category_with_parent(): void
    {
        Sanctum::actingAs($this->user);

        $parent = Category::factory()->create();

        $categoryData = [
            'name' => 'Sub Category',
            'slug' => 'sub-category',
            'parent_id' => $parent->id,
        ];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('categories', [
            'name' => 'Sub Category',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_authenticated_user_can_update_category(): void
    {
        Sanctum::actingAs($this->user);

        $category = Category::factory()->create();

        $updateData = [
            'name' => 'Updated Category',
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $category->id,
                'name' => 'Updated Category',
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
        ]);
    }

    public function test_authenticated_user_can_delete_category(): void
    {
        Sanctum::actingAs($this->user);

        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category deleted successfully.',
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_category(): void
    {
        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_update_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);

        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(401);
    }

    public function test_cannot_create_category_with_invalid_parent(): void
    {
        Sanctum::actingAs($this->user);

        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'parent_id' => 99999,
        ];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(422);
    }
}

