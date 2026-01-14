<?php

namespace App\Application\UseCases\Payment;

use App\Application\Services\PaymentGatewayServiceInterface;
use App\Domain\Exceptions\OrderNotFoundException;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Domain\Services\PaymentService;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class ProcessPaymentUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PaymentService $paymentService,
        private PaymentGatewayServiceInterface $paymentGateway
    ) {
    }

    public function execute(int $orderId, int $userId, array $data): Payment
    {
        return DB::transaction(function () use ($orderId, $userId, $data) {
            $order = $this->orderRepository->findById($orderId);

            if (!$order || $order->user_id !== $userId) {
                throw new OrderNotFoundException((string) $orderId);
            }

            $gatewayResponse = $this->paymentGateway->processPayment(
                $data['amount'],
                $data['payment_method'],
                ['order_id' => $orderId]
            );

            $paymentData = [
                'amount' => $data['amount'],
                'status' => $gatewayResponse['status'],
                'payment_method' => $data['payment_method'],
                'transaction_id' => $gatewayResponse['transaction_id'] ?? null,
            ];

            return $this->paymentService->processPayment($order, $paymentData);
        });
    }
}

