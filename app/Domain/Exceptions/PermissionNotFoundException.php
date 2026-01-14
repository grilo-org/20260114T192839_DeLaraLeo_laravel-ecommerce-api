<?php

namespace App\Domain\Exceptions;

use DomainException;

class PermissionNotFoundException extends DomainException
{
    public function __construct(string $identifier, string $type = 'slug')
    {
        $message = $type === 'id'
            ? "Permission with ID '{$identifier}' not found."
            : "Permission with slug '{$identifier}' not found.";

        parent::__construct($message);
    }
}

