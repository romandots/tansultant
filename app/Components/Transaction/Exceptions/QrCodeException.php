<?php

namespace App\Components\Transaction\Exceptions;

class QrCodeException extends Exception
{

    public static function becauseOfTochkaBankAdapterException(
        \App\Adapters\Banks\TochkaBank\Exceptions\TochkaBankAdapterException|\Exception $e
    ): self {
        return new self($e->getMessage(), $e->getData(), $e->getCode(), $e);
    }
}