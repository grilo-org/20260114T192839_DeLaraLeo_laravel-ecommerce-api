<?php

namespace App\Application\UseCases\Cart;

use App\Domain\Repositories\CartRepositoryInterface;
use App\Models\Cart;

class ClearCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {
    }

    public function execute(int $userId): Cart
    {
        $cart = $this->cartRepository->findOrCreateByUserId($userId);
        $this->cartRepository->clear($cart);

        return $this->cartRepository->findById($cart->id);
    }
}

