<?php

namespace App\Components\Transaction\Exceptions;

class AccountCannotBeSelectedException extends Exception
{
    public function __construct()
    {
        parent::__construct('account_cannot_be_selected');
    }
}