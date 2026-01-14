<?php

namespace App\Domain\Exceptions;

use DomainException;

class OrderNotFoundException extends DomainException
{
    public function __construct(string $identifier, string $type = 'id')
    {
        $message = $type === 'slug'
            ? "Order with slug '{$identifier}' not found."
            : "Order with ID '{$identifier}' not found.";

        parent::__construct($message);
    }
}

