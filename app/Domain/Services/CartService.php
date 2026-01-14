<?php

namespace App\Domain\Services;

use App\Domain\Exceptions\InsufficientStockException;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartService
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function validateStock(Product $product, int $requestedQuantity, ?CartItem $existingItem = null): void
    {
        $currentQuantityInCart = $existingItem ? $existingItem->quantity : 0;
        $newTotalQuantity = $currentQuantityInCart + $requestedQuantity;

        if ($newTotalQuantity > $product->stock) {
            throw new InsufficientStockException($newTotalQuantity, $product->stock);
        }
    }

    public function addOrUpdateItem(Cart $cart, Product $product, int $quantity): CartItem
    {
        $existingItem = $this->cartRepository->findItemByCartAndProduct($cart->id, $product->id);

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            $this->validateStock($product, $quantity, $existingItem);

            $this->cartRepository->updateItem($existingItem, [
                'quantity' => $newQuantity,
                'price_at_time' => $product->price,
            ]);

            return $existingItem->fresh();
        }

        $this->validateStock($product, $quantity);

        return $this->cartRepository->createItem([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price_at_time' => $product->price,
        ]);
    }

    public function calculateTotal(Cart $cart): float
    {
        return $cart->calculateTotal();
    }
}

