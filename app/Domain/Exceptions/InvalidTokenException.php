<?php

namespace App\Domain\Exceptions;

use DomainException;

class InvalidTokenException extends DomainException
{
    public function __construct(string $message = 'Invalid or expired token.')
    {
        parent::__construct($message);
    }
}

