<?php

namespace App\Domain\Exceptions;

use DomainException;

class UserNotFoundException extends DomainException
{
    public function __construct(string $identifier, string $type = 'id')
    {
        $message = $type === 'email'
            ? "User with email '{$identifier}' not found."
            : "User with ID '{$identifier}' not found.";

        parent::__construct($message);
    }
}

