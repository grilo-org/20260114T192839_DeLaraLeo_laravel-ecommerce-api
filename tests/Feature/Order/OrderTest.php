<?php

namespace Tests\Feature\Order;

use App\Domain\Exceptions\EmptyCartException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
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

    public function test_authenticated_user_can_create_order_from_cart(): void
    {
        Sanctum::actingAs($this->user);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_at_time' => 99.99,
        ]);

        $response = $this->postJson('/api/orders');

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'status',
                'total',
                'items' => [
                    [
                        'id',
                        'product_id',
                        'quantity',
                        'price_at_time',
                        'subtotal',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_cannot_create_order_with_empty_cart(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/orders');

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Cannot create order from empty cart.',
            ]);
    }

    public function test_cannot_create_order_with_insufficient_stock(): void
    {
        Sanctum::actingAs($this->user);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 15,
            'price_at_time' => 99.99,
        ]);

        $response = $this->postJson('/api/orders');

        $response->assertStatus(422);
    }

    public function test_order_creates_order_items_correctly(): void
    {
        Sanctum::actingAs($this->user);

        $product2 = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 5,
            'price' => 49.99,
        ]);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_at_time' => 99.99,
        ]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 3,
            'price_at_time' => 49.99,
        ]);

        $response = $this->postJson('/api/orders');

        $response->assertStatus(201);

        $order = Order::where('user_id', $this->user->id)->first();
        $this->assertDatabaseCount('order_items', 2);
        $this->assertEquals(2, $order->items->count());
    }

    public function test_order_updates_product_stock(): void
    {
        Sanctum::actingAs($this->user);

        $initialStock = $this->product->stock;
        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'price_at_time' => 99.99,
        ]);

        $this->postJson('/api/orders');

        $this->product->refresh();
        $this->assertEquals($initialStock - 3, $this->product->stock);
    }

    public function test_authenticated_user_can_list_orders(): void
    {
        Sanctum::actingAs($this->user);

        Order::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);
        
        $data = $response->json();
        $orders = data_get($data, 'data', $data);
        
        $this->assertIsArray($orders);
        $this->assertCount(3, $orders);
        $this->assertArrayHasKey('id', $orders[0]);
        $this->assertArrayHasKey('user_id', $orders[0]);
        $this->assertArrayHasKey('status', $orders[0]);
        $this->assertArrayHasKey('total', $orders[0]);
    }

    public function test_authenticated_user_can_view_order(): void
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'status',
                'total',
                'items',
            ])
            ->assertJson([
                'id' => $order->id,
                'user_id' => $this->user->id,
            ]);
    }

    public function test_user_cannot_view_other_user_order(): void
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => "Order with ID '{$order->id}' not found.",
            ]);
    }

    public function test_authenticated_user_can_update_order_status(): void
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->patchJson("/api/orders/{$order->id}/status", [
            'status' => 'processing',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'processing',
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }

    public function test_cancelling_order_reverts_stock(): void
    {
        Sanctum::actingAs($this->user);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'price_at_time' => 99.99,
        ]);

        $orderResponse = $this->postJson('/api/orders');
        $orderId = $orderResponse->json('id');
        $initialStock = $this->product->fresh()->stock;

        $this->deleteJson("/api/orders/{$orderId}");

        $this->product->refresh();
        $this->assertEquals($initialStock + 3, $this->product->stock);
    }

    public function test_authenticated_user_can_cancel_order(): void
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'cancelled',
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cannot_cancel_delivered_order(): void
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'delivered',
        ]);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(422);
    }

    public function test_unauthenticated_user_cannot_create_order(): void
    {
        $response = $this->postJson('/api/orders');

        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_list_orders(): void
    {
        $response = $this->getJson('/api/orders');

        $response->assertStatus(401);
    }

    public function test_order_total_is_calculated_correctly(): void
    {
        Sanctum::actingAs($this->user);

        $product2 = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 5,
            'price' => 49.99,
        ]);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_at_time' => 99.99,
        ]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 3,
            'price_at_time' => 49.99,
        ]);

        $response = $this->postJson('/api/orders');

        $expectedTotal = (2 * 99.99) + (3 * 49.99);
        $response->assertStatus(201)
            ->assertJson([
                'total' => $expectedTotal,
            ]);
    }
}

