<?php

namespace App\Components\Shift\Exceptions;

class ShiftDoesNotBelongToUserException extends Exception
{
    public function __construct() {
        parent::__construct('shift_does_not_belong_to_user', [], 403);
    }
}