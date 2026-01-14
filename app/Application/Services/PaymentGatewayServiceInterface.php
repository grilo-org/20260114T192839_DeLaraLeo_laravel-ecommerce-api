<?php

namespace App\Application\Services;

interface PaymentGatewayServiceInterface
{
    public function processPayment(float $amount, string $paymentMethod, array $metadata = []): array;
}

