<?php

declare(strict_types=1);

namespace App\Components\Credit;

use App\Common\BaseComponentService;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;

/**
 * @method Repository getRepository()
 */
class Service extends BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Credit::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    public function createWithdrawal(
        Customer $customer,
        int $amount,
        string $comment,
        User $user,
        ?Transaction $transaction = null
    ): Credit {
        return $this->createCredit(0 - $amount, $comment, $customer->id, $transaction?->id, $user);
    }

    public function createIncome(
        Customer $customer,
        int $amount,
        string $comment,
        User $user,
        ?Transaction $transaction
    ): Credit {
        return $this->createCredit($amount, $comment, $customer->id, $transaction?->id, $user);
    }

    private function createCredit(
        int $amount,
        string $comment,
        string $customerId,
        ?string $transactionId,
        User $user,
    ): Credit {
        $creditDto = new \App\Components\Credit\Dto($user);
        $creditDto->name = $comment;
        $creditDto->amount = $amount;
        $creditDto->customer_id = $customerId;
        $creditDto->transaction_id = $transactionId;

        return $this->create($creditDto);
    }
}