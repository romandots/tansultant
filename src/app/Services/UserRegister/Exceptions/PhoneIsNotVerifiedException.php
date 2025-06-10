<?php

namespace App\Services\UserRegister\Exceptions;

use App\Exceptions\BaseException;

class PhoneIsNotVerifiedException extends BaseException
{

    public function __construct()
    {
        parent::__construct('phone_is_not_verified', null, 409);
    }
}