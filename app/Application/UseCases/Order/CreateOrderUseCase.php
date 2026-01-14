<?php

namespace App\Application\UseCases\Order;

use App\Domain\Repositories\CartRepositoryInterface;
use App\Domain\Services\OrderService;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CreateOrderUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private OrderService $orderService
    ) {
    }

    public function execute(int $userId): Order
    {
        return DB::transaction(function () use ($userId) {
            $cart = $this->cartRepository->findByUserId($userId);

            if (!$cart) {
                $cart = $this->cartRepository->findOrCreateByUserId($userId);
            }

            $order = $this->orderService->createOrderFromCart($userId, $cart);

            $this->cartRepository->clear($cart);

            return $order;
        });
    }
}

