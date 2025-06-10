<?php

namespace App\Components\Visit\Exceptions;

class CannotDeletePaidVisit extends Exception
{
    public function __construct()
    {
        parent::__construct('cannot_delete_paid_visit');
    }
}