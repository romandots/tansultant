<?php

namespace App\Listeners;

use App\Components\Loader;

class SubscriptionListener
{
    protected \App\Components\Subscription\Facade $subscriptions;

    public function __construct()
    {
        $this->subscriptions = Loader::subscriptions();
    }
}
