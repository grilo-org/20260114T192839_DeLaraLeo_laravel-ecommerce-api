<?php

namespace App\Domain\Services;

use App\Domain\Exceptions\EmptyCartException;
use App\Domain\Exceptions\InsufficientStockException;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;

class OrderService
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function validateCartNotEmpty(Cart $cart): void
    {
        if ($cart->items->isEmpty()) {
            throw new EmptyCartException();
        }
    }

    public function validateStockForCart(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            $product = $this->productRepository->findById($item->product_id);

            if (!$product) {
                continue;
            }

            if ($item->quantity > $product->stock) {
                throw new InsufficientStockException($item->quantity, $product->stock);
            }
        }
    }

    public function createOrderFromCart(int $userId, Cart $cart): Order
    {
        $this->validateCartNotEmpty($cart);
        $this->validateStockForCart($cart);

        $total = $cart->calculateTotal();

        $order = $this->orderRepository->create([
            'user_id' => $userId,
            'status' => 'pending',
            'total' => $total,
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price_at_time' => $item->price_at_time,
            ]);

            $product = $this->productRepository->findById($item->product_id);
            if ($product) {
                $this->productRepository->update($product, [
                    'stock' => $product->stock - $item->quantity,
                ]);
            }
        }

        return $order->fresh(['items.product', 'user', 'payment']);
    }

    public function calculateTotal(Order $order): float
    {
        return $order->calculateTotal();
    }
}

