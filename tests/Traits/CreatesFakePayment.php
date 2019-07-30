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
use App\Models\Payment;

/**
 * Class CreatesFakePayment
 * @package Tests\Traits
 */
trait CreatesFakePayment
{
    /**
     * @param int|null $amount
     * @param Account|null $account
     * @param array|null $attributes
     * @return Payment
     */
    private function createFakePayment(?int $amount = null, ?Account $account = null, ?array $attributes = []): Payment
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

        return \factory(Payment::class)->create($attributes);
    }

    /**
     * @param int|null $amount
     * @param Account|null $account
     * @param array|null $attributes
     * @return Bonus
     */
    private function createFakeBonus(?int $amount, ?Account $account, ?array $attributes = []): Bonus
    {
        if (null !== $amount) {
            $attributes['amount'] = $amount;
        }

        if (null !== $account) {
            $attributes['account_id'] = $account->id;
        }

        return \factory(Bonus::class)->create($attributes);
    }
}
