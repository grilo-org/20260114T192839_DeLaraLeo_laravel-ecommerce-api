<?php

namespace App\Domain\Repositories;

use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment;

    public function findByOrderId(int $orderId): ?Payment;

    public function create(array $data): Payment;

    public function update(Payment $payment, array $data): bool;
}

