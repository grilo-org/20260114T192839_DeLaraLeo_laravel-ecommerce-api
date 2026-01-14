<?php

namespace App\Application\UseCases\Cart;

use App\Domain\Exceptions\ProductNotFoundException;
use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Domain\Services\CartService;
use App\Models\Cart;

class AddToCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository,
        private CartService $cartService
    ) {
    }

    /**
     * Add a product to the user's cart.
     *
     * @param int $userId
     * @param array{product_id: int, quantity: int} $data
     * @return Cart
     * @throws ProductNotFoundException
     */
    public function execute(int $userId, array $data): Cart
    {
        $product = $this->productRepository->findById($data['product_id']);

        if (!$product) {
            throw new ProductNotFoundException((string) $data['product_id'], 'id');
        }

        $cart = $this->cartRepository->findOrCreateByUserId($userId);
        $this->cartService->addOrUpdateItem($cart, $product, $data['quantity']);

        return $this->cartRepository->findById($cart->id);
    }
}

