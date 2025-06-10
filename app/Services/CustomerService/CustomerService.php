<?php

namespace App\Services\CustomerService;

use App\Models\Person;
use App\Notifications\CustomerService\BalanceNotification;
use App\Notifications\CustomerService\SubscriptionsNotification;

class CustomerService
{
    public function sendBalanceNotification(Person $person): void
    {
        $person->notify(new BalanceNotification($person));
    }

    public function sendSubscriptionsNotification(Person $person): void
    {
        $person->notify(new SubscriptionsNotification($person));
    }
}