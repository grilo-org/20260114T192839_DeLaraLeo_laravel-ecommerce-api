<?php

namespace App\Application\Services;

class StripePaymentGateway implements PaymentGatewayServiceInterface
{
    public function processPayment(float $amount, string $paymentMethod, array $metadata = []): array
    {
        return [
            'success' => true,
            'transaction_id' => 'mock_' . uniqid(),
            'status' => 'paid',
        ];
    }
}

