<?php

namespace App\Domain\Exceptions;

use DomainException;

class PaymentNotFoundException extends DomainException
{
    public function __construct(string $identifier, string $type = 'id')
    {
        $message = $type === 'order_id'
            ? "Payment for order ID '{$identifier}' not found."
            : "Payment with ID '{$identifier}' not found.";

        parent::__construct($message);
    }
}

