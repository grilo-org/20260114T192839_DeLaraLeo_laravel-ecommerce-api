<?php

namespace App\Domain\Exceptions;

use DomainException;

class RoleNotFoundException extends DomainException
{
    public function __construct(string $identifier, string $type = 'slug')
    {
        $message = $type === 'id'
            ? "Role with ID '{$identifier}' not found."
            : "Role with slug '{$identifier}' not found.";

        parent::__construct($message);
    }
}

