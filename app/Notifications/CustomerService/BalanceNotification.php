<?php

namespace App\Notifications\CustomerService;

use function morphos\Russian\pluralize;

class BalanceNotification extends \App\Notifications\TelegramNotification
{
    public function getMessage(): string
    {
        $this
            ->getLogger()
            ->debug("CustomerService: Getting balance for person {$this->person->name} ({$this->person->id})");

        $customer = $this->getCustomer();
        $balance = pluralize($customer->credits_sum, 'рубль');
        $bonusBalance = pluralize($customer->pending_bonuses_sum, 'бонусный рубль');

        return trans('customer_service.balance', ['balance' => $balance, 'bonus_balance' => $bonusBalance]);
    }
}