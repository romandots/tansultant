<?php

declare(strict_types=1);

namespace App\Components\Account\Exceptions;

use App\Exceptions\BaseException;

class InsufficientFundsAccountException extends BaseException
{
    /**
     * InsufficientFundsAccountServiceException constructor.
     * @param \App\Models\Account $account
     * @param int $availableAmount
     * @param int $requiredAmount
     */
    public function __construct(\App\Models\Account $account, int $availableAmount, int $requiredAmount)
    {
        parent::__construct('account_has_insufficient_funds', [
            'account_id' => $account->id,
            'account_name' => $account->name,
            'available_amount' => $availableAmount,
            'required_amount' => $requiredAmount
        ]);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }
}
