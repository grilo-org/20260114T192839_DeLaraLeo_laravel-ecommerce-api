<?php

namespace App\Domain\Exceptions;

use DomainException;

class CartItemNotFoundException extends DomainException
{
    public function __construct(string $identifier)
    {
        $message = "Cart item with ID '{$identifier}' not found.";

        parent::__construct($message);
    }
}

