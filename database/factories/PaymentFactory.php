<?php

namespace Database\Factories;

use App\Domain\ValueObjects\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'status' => PaymentStatus::PENDING,
            'payment_method' => $this->faker->randomElement(['credit_card', 'debit_card', 'pix', 'bank_transfer']),
            'transaction_id' => 'mock_' . $this->faker->uuid(),
        ];
    }
}

