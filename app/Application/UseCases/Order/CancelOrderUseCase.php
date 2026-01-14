<?php

namespace App\Application\UseCases\Order;

use App\Domain\Exceptions\OrderNotFoundException;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Domain\Repositories\PaymentRepositoryInterface;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CancelOrderUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private ProductRepositoryInterface $productRepository
    ) {
    }

    public function execute(int $orderId, int $userId): Order
    {
        return DB::transaction(function () use ($orderId, $userId) {
            $order = $this->orderRepository->findById($orderId);

            if (!$order || $order->user_id !== $userId) {
                throw new OrderNotFoundException((string) $orderId);
            }

            if (in_array($order->status->value, ['delivered', 'refunded'])) {
                throw ValidationException::withMessages([
                    'order' => ['Cannot cancel order that has been delivered or refunded.'],
                ]);
            }

            $payment = $this->paymentRepository->findByOrderId($order->id);
            if ($payment && $payment->status->value === 'paid') {
                $this->paymentRepository->update($payment, [
                    'status' => 'refunded',
                ]);
            }

            foreach ($order->items as $item) {
                $product = $this->productRepository->findById($item->product_id);
                if ($product) {
                    $this->productRepository->update($product, [
                        'stock' => $product->stock + $item->quantity,
                    ]);
                }
            }

            $this->orderRepository->update($order, [
                'status' => 'cancelled',
            ]);

            return $order->fresh(['items.product', 'user', 'payment']);
        });
    }
}

