<?php

namespace App\Domain\Exceptions;

use DomainException;

class OrderAlreadyPaidException extends DomainException
{
    public function __construct(int $orderId)
    {
        parent::__construct("Order with ID '{$orderId}' has already been paid.");
    }
}

