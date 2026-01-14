<?php

namespace App\Application\UseCases\Order;

use App\Domain\Exceptions\OrderNotFoundException;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Models\Order;

class GetOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function execute(int $orderId, int $userId): Order
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order) {
            throw new OrderNotFoundException((string) $orderId);
        }

        if ($order->user_id !== $userId) {
            throw new OrderNotFoundException((string) $orderId);
        }

        return $order;
    }
}

