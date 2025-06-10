<?php

namespace App\Components\Person\Exceptions;

use App\Exceptions\SimpleValidationException;

class PhoneIsRequired extends SimpleValidationException
{

    public function __construct()
    {
        parent::__construct('phone', 'required');
    }
}