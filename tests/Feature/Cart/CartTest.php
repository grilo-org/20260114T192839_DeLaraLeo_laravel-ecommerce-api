<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 10,
            'price' => 99.99,
        ]);
    }

    public function test_authenticated_user_can_get_cart(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'items',
                'total',
                'items_count',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_authenticated_user_can_add_product_to_cart(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'items' => [
                    [
                        'id',
                        'quantity',
                        'price_at_time',
                        'subtotal',
                        'product',
                    ],
                ],
                'total',
            ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    public function test_adding_same_product_updates_quantity(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseCount('cart_items', 1);
    }

    public function test_cannot_add_more_than_available_stock(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 11,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Insufficient stock. Requested: 11, Available: 10.',
            ]);
    }

    public function test_cannot_add_more_when_quantity_exceeds_stock(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 8,
        ]);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Insufficient stock. Requested: 13, Available: 10.',
            ]);
    }

    public function test_authenticated_user_can_remove_item_from_cart(): void
    {
        Sanctum::actingAs($this->user);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        $item = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->deleteJson("/api/cart/items/{$item->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $item->id,
        ]);
    }

    public function test_authenticated_user_can_clear_cart(): void
    {
        Sanctum::actingAs($this->user);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->count(3)->create(['cart_id' => $cart->id]);

        $response = $this->postJson('/api/cart/clear');

        $response->assertStatus(200)
            ->assertJson([
                'items' => [],
                'total' => 0,
                'items_count' => 0,
            ]);

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_cart_total_is_calculated_correctly(): void
    {
        Sanctum::actingAs($this->user);

        $product2 = Product::factory()->create([
            'category_id' => $this->category->id,
            'price' => 49.99,
            'stock' => 5,
        ]);

        $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $this->postJson('/api/cart/add', [
            'product_id' => $product2->id,
            'quantity' => 3,
        ]);

        $response = $this->getJson('/api/cart');

        $expectedTotal = (2 * 99.99) + (3 * 49.99);
        $response->assertStatus(200)
            ->assertJson([
                'total' => $expectedTotal,
            ]);
    }

    public function test_unauthenticated_user_cannot_access_cart(): void
    {
        $response = $this->getJson('/api/cart');

        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_add_to_cart(): void
    {
        $response = $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(401);
    }

    public function test_add_to_cart_requires_valid_product(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => 99999,
            'quantity' => 1,
        ]);

        $response->assertStatus(422);
    }

    public function test_add_to_cart_requires_positive_quantity(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 0,
        ]);

        $response->assertStatus(422);
    }

    public function test_price_at_time_is_stored_when_adding_item(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson('/api/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $this->product->update(['price' => 149.99]);

        $item = CartItem::where('product_id', $this->product->id)->first();
        $this->assertEquals(99.99, $item->price_at_time);
    }

    public function test_cannot_remove_item_from_other_user_cart(): void
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();
        $otherCart = Cart::factory()->create(['user_id' => $otherUser->id]);
        $otherItem = CartItem::factory()->create([
            'cart_id' => $otherCart->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->deleteJson("/api/cart/items/{$otherItem->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => "Cart item with ID '{$otherItem->id}' not found.",
            ]);
    }
}

