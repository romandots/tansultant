<?php
/**
 * File: CreatesFakePayment.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Account;
use App\Models\Bonus;
use App\Models\Transaction;

/**
 * Class CreatesFakePayment
 * @package Tests\Traits
 */
trait CreatesFakeTransaction
{
    /**
     * @param int|null $amount
     * @param Account|null $account
     * @param array|null $attributes
     * @return Transaction
     */
    protected function createFakeTransaction(?int $amount = null, ?Account $account = null, ?array $attributes = []): Transaction
    {
        if (null !== $amount) {
            $attributes['amount'] = $amount;
        }

        if (null !== $account) {
            $attributes['account_id'] = $account->id;
        }

        if (!isset($attributes['account_id'])) {
            $attributes['account_id'] = $this->createFakeAccount()->id;
        }

        if (!isset($attributes['user_id'])) {
            $attributes['user_id'] = $this->createFakeUser()->id;
        }

        return Transaction::factory()->create($attributes);
    }

    /**
     * @param int|null $amount
     * @param Account|null $fromAccount
     * @param Account|null $toAccount
     * @param array|null $attributes
     * @return array
     * @throws \Exception
     */
    protected function createFakeInternalTransaction(
        ?int $amount = null,
        ?Account $fromAccount = null,
        ?Account $toAccount = null,
        ?array $attributes = []
    ):
    array {
        $payment = $this->createFakeTransaction(0 - $amount, $fromAccount, $attributes);
        $attributes = $payment->toArray();
        $attributes['id'] = \uuid();
        $attributes['related_id'] = $payment->id;
        $related = $this->createFakeTransaction($amount, $toAccount, $attributes);
        $payment->related_id = $related->id;

        return [$payment, $related];
    }

    /**
     * @param int|null $amount
     * @param Account|null $account
     * @param array|null $attributes
     * @return Bonus
     */
    protected function createFakeBonus(?int $amount = null, ?Account $account = null, ?array $attributes = []): Bonus
    {
        if (null !== $amount) {
            $attributes['amount'] = $amount;
        }

        if (null !== $account) {
            $attributes['account_id'] = $account->id;
        }

        return Bonus::factory()->create($attributes);
    }
}
