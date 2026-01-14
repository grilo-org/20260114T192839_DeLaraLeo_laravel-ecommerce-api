<?php

namespace App\Domain\Repositories;

use App\Models\Cart;
use App\Models\CartItem;

interface CartRepositoryInterface
{
    public function findByUserId(int $userId): ?Cart;

    public function findOrCreateByUserId(int $userId): Cart;

    public function findById(int $id): ?Cart;

    public function create(array $data): Cart;

    public function clear(Cart $cart): bool;

    public function findItemById(int $itemId): ?CartItem;

    public function findItemByCartAndProduct(int $cartId, int $productId): ?CartItem;

    public function createItem(array $data): CartItem;

    public function updateItem(CartItem $item, array $data): bool;

    public function deleteItem(CartItem $item): bool;
}

