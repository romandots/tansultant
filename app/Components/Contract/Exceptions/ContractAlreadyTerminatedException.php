<?php

declare(strict_types=1);

namespace App\Components\Contract\Exceptions;

class ContractAlreadyTerminatedException extends ContractException
{
    /**
     * ContractAlreadySignedException constructor.
     */
    public function __construct()
    {
        parent::__construct('contract_already_terminated');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }
}
