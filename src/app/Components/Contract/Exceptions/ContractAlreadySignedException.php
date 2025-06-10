<?php

declare(strict_types=1);

namespace App\Components\Contract\Exceptions;

class ContractAlreadySignedException extends ContractException
{
    /**
     * ContractAlreadySignedException constructor.
     */
    public function __construct()
    {
        parent::__construct('contract_already_signed');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }
}
