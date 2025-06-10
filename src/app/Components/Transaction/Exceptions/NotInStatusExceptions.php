<?php

namespace App\Components\Transaction\Exceptions;

use App\Models\Enum\TransactionStatus;

class NotInStatusExceptions extends Exception
{
    public function __construct(public readonly TransactionStatus $status)
    {
        parent::__construct(
            'transaction_is_not_in_requested_status',
            [
                'status' => $status->value,
            ],
            422
        );
    }
}