<?php

namespace App\Domain\Exceptions;

use DomainException;

class TokenExpiredException extends DomainException
{
    public function __construct(string $message = 'Token has expired.')
    {
        parent::__construct($message);
    }
}

