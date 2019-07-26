<?php
/**
 * File: ContractAlreadySignedException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Contract\Exceptions;

/**
 * Class ContractAlreadySignedException
 * @package App\Service\Contract\Exceptions
 */
class ContractAlreadySignedException extends ContractServiceException
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
