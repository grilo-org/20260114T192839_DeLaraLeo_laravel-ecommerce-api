<?php

namespace App\Domain\Exceptions;

use DomainException;

class EmptyCartException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Cannot create order from empty cart.');
    }
}

