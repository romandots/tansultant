<?php

namespace App\Components\Customer\Exceptions;

use App\Exceptions\SimpleValidationException;

class PersonHasNoPhoneException extends SimpleValidationException
{
    public function __construct()
    {
        parent::__construct('person_id', 'no_phone');
    }
}