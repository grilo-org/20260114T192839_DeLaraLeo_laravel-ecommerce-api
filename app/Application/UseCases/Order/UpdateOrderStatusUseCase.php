<?php

namespace App\Application\UseCases\Order;

use App\Domain\Exceptions\OrderNotFoundException;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class UpdateOrderStatusUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function execute(int $orderId, int $userId, string $status): Order
    {
        return DB::transaction(function () use ($orderId, $userId, $status) {
            $order = $this->orderRepository->findById($orderId);

            if (!$order || $order->user_id !== $userId) {
                throw new OrderNotFoundException((string) $orderId);
            }

            $this->orderRepository->update($order, ['status' => $status]);

            if ($status === 'cancelled') {
                foreach ($order->items as $item) {
                    $product = $this->productRepository->findById($item->product_id);
                    if ($product) {
                        $this->productRepository->update($product, [
                            'stock' => $product->stock + $item->quantity,
                        ]);
                    }
                }
            }

            return $order->fresh(['items.product', 'user', 'payment']);
        });
    }
}

