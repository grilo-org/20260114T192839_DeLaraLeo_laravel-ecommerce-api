<?php

namespace App\Domain\Services;

use App\Domain\Exceptions\OrderAlreadyPaidException;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Domain\Repositories\PaymentRepositoryInterface;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PaymentRepositoryInterface $paymentRepository
    ) {
    }

    public function validateOrderNotPaid(Order $order): void
    {
        $existingPayment = $this->paymentRepository->findByOrderId($order->id);

        if ($existingPayment && $existingPayment->status->value === 'paid') {
            throw new OrderAlreadyPaidException($order->id);
        }
    }

    public function validateAmount(Order $order, float $amount): void
    {
        if (abs($amount - $order->total) > 0.01) {
            throw ValidationException::withMessages([
                'amount' => ['Payment amount does not match order total.'],
            ]);
        }
    }

    public function processPayment(Order $order, array $paymentData): Payment
    {
        $this->validateOrderNotPaid($order);
        $this->validateAmount($order, $paymentData['amount']);

        $payment = $this->paymentRepository->create([
            'order_id' => $order->id,
            'amount' => $paymentData['amount'],
            'status' => $paymentData['status'],
            'payment_method' => $paymentData['payment_method'],
            'transaction_id' => $paymentData['transaction_id'] ?? null,
        ]);

        if ($payment->status->value === 'paid') {
            $this->orderRepository->update($order, [
                'status' => 'processing',
            ]);
            $order->refresh();
        }

        return $payment->fresh(['order']);
    }
}

