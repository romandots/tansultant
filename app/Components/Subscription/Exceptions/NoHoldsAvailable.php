<?php

namespace App\Components\Subscription\Exceptions;

class NoHoldsAvailable extends Exception
{
    public function __construct(public readonly \App\Models\Subscription $subscription)
    {
        parent::__construct('no_holds_available', [
            'subscription' => $this->subscription->name,
        ]);
    }
}