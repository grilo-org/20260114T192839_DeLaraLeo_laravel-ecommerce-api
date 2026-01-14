<?php

namespace App\Domain\Exceptions;

use DomainException;

class CategoryNotFoundException extends DomainException
{
    public function __construct(string $identifier, string $type = 'id')
    {
        $message = $type === 'slug'
            ? "Category with slug '{$identifier}' not found."
            : "Category with ID '{$identifier}' not found.";

        parent::__construct($message);
    }
}

