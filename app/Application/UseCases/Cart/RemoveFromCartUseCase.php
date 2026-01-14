<?php

namespace App\Application\UseCases\Cart;

use App\Domain\Exceptions\CartItemNotFoundException;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Models\Cart;

class RemoveFromCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {
    }

    public function execute(int $userId, int $itemId): Cart
    {
        $cart = $this->cartRepository->findByUserId($userId);

        if (!$cart) {
            $cart = $this->cartRepository->findOrCreateByUserId($userId);
        }

        $item = $this->cartRepository->findItemById($itemId);

        if (!$item || $item->cart_id !== $cart->id) {
            throw new CartItemNotFoundException((string) $itemId);
        }

        $this->cartRepository->deleteItem($item);

        return $this->cartRepository->findById($cart->id);
    }
}

