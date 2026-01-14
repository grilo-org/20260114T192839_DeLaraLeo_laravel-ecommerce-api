<?php

namespace App\Http\Resources\Payment;

use App\Http\Resources\Order\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'amount' => (float) $this->amount,
            'status' => $this->status->value,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'order' => $this->whenLoaded('order', function () {
                return new OrderResource($this->order);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

