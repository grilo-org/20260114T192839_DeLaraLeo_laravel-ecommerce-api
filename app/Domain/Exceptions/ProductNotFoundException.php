<?php

namespace App\Domain\Exceptions;

use DomainException;

class ProductNotFoundException extends DomainException
{
    public function __construct(string $identifier, string $type = 'id')
    {
        $message = $type === 'slug'
            ? "Product with slug '{$identifier}' not found."
            : "Product with ID '{$identifier}' not found.";

        parent::__construct($message);
    }
}

