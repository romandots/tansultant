<?php

namespace App\Components\Transaction\Exceptions;

class NotInTransferTypeException extends Exception
{
    public function __construct(
        public readonly \App\Models\Enum\TransactionTransferType $transactionTransferType
    ) {
        parent::__construct(
            'transaction_of_wrong_type',
            ['transaction_type' => $transactionTransferType->value],
            422
        );
    }
}