<?php

namespace App\Listeners;

use App\Events\Hold\HoldCreatedEvent;
use App\Events\Hold\HoldDeletedEvent;
use App\Events\Hold\HoldEndedEvent;
use App\Events\Hold\HoldEvent;

class UpdateSubscriptionHoldStatus extends SubscriptionListener
{
    public function handle(HoldEvent $holdEvent): void
    {
        $logger = \app('log');
        $logger->debug('Handling UpdateSubscriptionHoldStatus listener');

        $hold = $holdEvent->hold;
        $user = $holdEvent->user;
        $subscription = $hold->load('subscription')->subscription;

        if (null === $subscription) {
            $logger->error('Tried to update subscription status, but hold has no subscription attached', [
                'hold_id' => $hold->id,
            ]);
            return;
        }

        match (true) {
            $holdEvent instanceof HoldCreatedEvent => $this->subscriptions->hold(
                $subscription,
                $hold,
                $user
            ),
            $holdEvent instanceof HoldEndedEvent => $this->subscriptions->unhold(
                $subscription,
                $hold->getDuration(),
                $user
            ),
            $holdEvent instanceof HoldDeletedEvent => $this->subscriptions->unhold(
                $subscription,
                0,
                $user
            ),
        };

    }
}
