<?php

namespace App\Application\UseCases\Payment;

use App\Domain\Exceptions\OrderNotFoundException;
use App\Domain\Exceptions\PaymentNotFoundException;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Domain\Repositories\PaymentRepositoryInterface;
use App\Models\Payment;

class GetPaymentUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PaymentRepositoryInterface $paymentRepository
    ) {
    }

    public function execute(int $orderId, int $userId): Payment
    {
        $order = $this->orderRepository->findById($orderId);

        if (!$order || $order->user_id !== $userId) {
            throw new OrderNotFoundException((string) $orderId);
        }

        $payment = $this->paymentRepository->findByOrderId($orderId);

        if (!$payment) {
            throw new PaymentNotFoundException((string) $orderId, 'order_id');
        }

        return $payment;
    }
}

