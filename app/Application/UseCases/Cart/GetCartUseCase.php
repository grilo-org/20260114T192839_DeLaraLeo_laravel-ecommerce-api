<?php

namespace App\Application\UseCases\Cart;

use App\Domain\Repositories\CartRepositoryInterface;
use App\Models\Cart;

class GetCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository
    ) {
    }

    public function execute(int $userId): Cart
    {
        $cart = $this->cartRepository->findOrCreateByUserId($userId);
        
        return $cart->load(['items.product']);
    }
}

