<?php
/**
 * File: CreatesFakeAccount.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-30
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Account;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

/**
 * Trait CreatesFakeAccount
 * @package Tests\Unit
 */
trait CreatesFakeAccount
{
    /**
     * @param array|null $attributes
     * @return Account
     */
    private function createFakeAccount(?array $attributes = []): Account
    {
        return \factory(Account::class)->create($attributes);
    }

    /**
     * @param int $amount
     * @param User|null $user
     * @param array|null $attributes
     * @return Account
     */
    private function createFakeAccountWithBalance(int $amount, ?User $user = null, ?array $attributes = []): Account
    {
        $account = $this->createFakeAccount($attributes);
        $user =  $user ?: $this->createFakeUser();
        $this->createFakePayment($amount, $account, [
            'status' => Payment::STATUS_CONFIRMED,
            'confirmed_at' => Carbon::now(),
            'user_id' => $user->id
        ]);

        return $account;
    }
}
