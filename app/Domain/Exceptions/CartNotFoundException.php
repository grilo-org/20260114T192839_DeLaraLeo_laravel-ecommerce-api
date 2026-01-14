<?php

namespace App\Domain\Exceptions;

use DomainException;

class CartNotFoundException extends DomainException
{
    public function __construct(string $identifier, string $type = 'id')
    {
        $message = $type === 'user_id'
            ? "Cart for user ID '{$identifier}' not found."
            : "Cart with ID '{$identifier}' not found.";

        parent::__construct($message);
    }
}

