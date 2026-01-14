<?php

namespace App\Domain\Exceptions;

use DomainException;

class InsufficientStockException extends DomainException
{
    public function __construct(int $requested, int $available)
    {
        $message = "Insufficient stock. Requested: {$requested}, Available: {$available}.";

        parent::__construct($message);
    }
}

