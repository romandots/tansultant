<?php

declare(strict_types=1);

namespace App\Components\User\Exceptions;

class OldPasswordInvalidException extends Exception
{
    /**
     * OldPasswordInvalidException constructor.
     */
    public function __construct()
    {
        parent::__construct('old_password_invalid', null, 409);
    }
}
