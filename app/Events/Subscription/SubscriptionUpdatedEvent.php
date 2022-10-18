<?php

namespace App\Events\Subscription;

use App\Models\Subscription;

class SubscriptionUpdatedEvent extends \App\Events\BaseEvent
{
    public function __construct(
        public readonly Subscription $subscription
    ) {
    }

    public function getChannelName(): string
    {
        return \sprintf('subscription.%s', $this->subscription->id);
    }
}