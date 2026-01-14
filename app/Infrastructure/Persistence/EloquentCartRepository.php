<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;

class EloquentCartRepository implements CartRepositoryInterface
{
    public function findByUserId(int $userId): ?Cart
    {
        return Cart::with(['items.product'])->where('user_id', $userId)->first();
    }

    public function findOrCreateByUserId(int $userId): Cart
    {
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId]
        );

        if (!$cart->relationLoaded('items')) {
            $cart->load(['items.product']);
        }

        return $cart;
    }

    public function findById(int $id): ?Cart
    {
        return Cart::with(['items.product'])->find($id);
    }

    public function create(array $data): Cart
    {
        return Cart::create($data);
    }

    public function clear(Cart $cart): bool
    {
        return $cart->items()->delete();
    }

    public function findItemById(int $itemId): ?CartItem
    {
        return CartItem::with('product')->find($itemId);
    }

    public function findItemByCartAndProduct(int $cartId, int $productId): ?CartItem
    {
        return CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();
    }

    public function createItem(array $data): CartItem
    {
        return CartItem::create($data);
    }

    public function updateItem(CartItem $item, array $data): bool
    {
        return $item->update($data);
    }

    public function deleteItem(CartItem $item): bool
    {
        return $item->delete();
    }
}

