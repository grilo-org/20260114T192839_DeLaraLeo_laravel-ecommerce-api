<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\PaymentRepositoryInterface;
use App\Models\Payment;

class EloquentPaymentRepository implements PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment
    {
        return Payment::with('order')->find($id);
    }

    public function findByOrderId(int $orderId): ?Payment
    {
        return Payment::with('order')->where('order_id', $orderId)->first();
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(Payment $payment, array $data): bool
    {
        return $payment->update($data);
    }
}

