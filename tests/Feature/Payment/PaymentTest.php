<?php

namespace Tests\Feature\Payment;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentTest extends TestCase
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

    public function test_authenticated_user_can_process_payment(): void
    {
        Sanctum::actingAs($this->user);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_at_time' => 99.99,
        ]);

        $orderResponse = $this->postJson('/api/orders');
        $orderId = $orderResponse->json('id');
        $orderTotal = $orderResponse->json('total');

        $response = $this->postJson("/api/orders/{$orderId}/payment", [
            'amount' => $orderTotal,
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'order_id',
                'amount',
                'status',
                'payment_method',
                'transaction_id',
            ])
            ->assertJson([
                'status' => 'paid',
                'amount' => $orderTotal,
            ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $orderId,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'processing',
        ]);
    }

    public function test_cannot_process_payment_with_wrong_amount(): void
    {
        Sanctum::actingAs($this->user);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_at_time' => 99.99,
        ]);

        $orderResponse = $this->postJson('/api/orders');
        $orderId = $orderResponse->json('id');

        $response = $this->postJson("/api/orders/{$orderId}/payment", [
            'amount' => 50.00,
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(422);
    }

    public function test_cannot_process_payment_twice(): void
    {
        Sanctum::actingAs($this->user);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_at_time' => 99.99,
        ]);

        $orderResponse = $this->postJson('/api/orders');
        $orderId = $orderResponse->json('id');
        $orderTotal = $orderResponse->json('total');

        $this->postJson("/api/orders/{$orderId}/payment", [
            'amount' => $orderTotal,
            'payment_method' => 'credit_card',
        ]);

        $response = $this->postJson("/api/orders/{$orderId}/payment", [
            'amount' => $orderTotal,
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => "Order with ID '{$orderId}' has already been paid.",
            ]);
    }

    public function test_authenticated_user_can_get_payment(): void
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create(['user_id' => $this->user->id]);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 199.98,
            'status' => 'paid',
        ]);

        $response = $this->getJson("/api/orders/{$order->id}/payment");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'order_id',
                'amount',
                'status',
                'payment_method',
                'transaction_id',
            ])
            ->assertJson([
                'id' => $payment->id,
                'order_id' => $order->id,
            ]);
    }

    public function test_cannot_get_payment_for_nonexistent_order(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/orders/99999/payment');

        $response->assertStatus(422)
            ->assertJson([
                'message' => "Order with ID '99999' not found.",
            ]);
    }

    public function test_cannot_get_payment_for_order_without_payment(): void
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/orders/{$order->id}/payment");

        $response->assertStatus(422)
            ->assertJson([
                'message' => "Payment for order ID '{$order->id}' not found.",
            ]);
    }

    public function test_user_cannot_get_payment_for_other_user_order(): void
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);
        Payment::factory()->create(['order_id' => $order->id]);

        $response = $this->getJson("/api/orders/{$order->id}/payment");

        $response->assertStatus(422)
            ->assertJson([
                'message' => "Order with ID '{$order->id}' not found.",
            ]);
    }

    public function test_unauthenticated_user_cannot_process_payment(): void
    {
        $order = Order::factory()->create();

        $response = $this->postJson("/api/orders/{$order->id}/payment", [
            'amount' => 100.00,
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_get_payment(): void
    {
        $order = Order::factory()->create();

        $response = $this->getJson("/api/orders/{$order->id}/payment");

        $response->assertStatus(401);
    }

    public function test_payment_requires_valid_payment_method(): void
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson("/api/orders/{$order->id}/payment", [
            'amount' => 100.00,
            'payment_method' => 'invalid_method',
        ]);

        $response->assertStatus(422);
    }

    public function test_payment_requires_positive_amount(): void
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->postJson("/api/orders/{$order->id}/payment", [
            'amount' => 0,
            'payment_method' => 'credit_card',
        ]);

        $response->assertStatus(422);
    }

    public function test_payment_updates_order_status_to_processing(): void
    {
        Sanctum::actingAs($this->user);

        $cart = Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price_at_time' => 99.99,
        ]);

        $orderResponse = $this->postJson('/api/orders');
        $orderId = $orderResponse->json('id');
        $orderTotal = $orderResponse->json('total');

        $this->postJson("/api/orders/{$orderId}/payment", [
            'amount' => $orderTotal,
            'payment_method' => 'credit_card',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $orderId,
            'status' => 'processing',
        ]);
    }
}

