<?php

namespace App\Listeners;

use App\Events\Visit\VisitEvent;

class UpdateSubscriptionPendingStatus extends SubscriptionListener
{
    public function handle(VisitEvent $visitEvent): void
    {
        $logger = \app('log');
        $logger->debug('Handling UpdateSubscriptionPendingStatus listener');

        $visit = $visitEvent->visit;
        $user = $visitEvent->user;

        if (null === $visit->subscription_id){
            return;
        }

        $subscription = $visit->load('subscription')->subscription;
        $subscriptionHasVisits = (bool)$subscription->visits->count();

        if (null === $subscription) {
            $logger->error('Tried to update subscription status, but visit has no subscription attached', [
                'visit_id' => $visit->id,
            ]);
            return;
        }

        match ($subscriptionHasVisits) {
            true => $this->subscriptions->activatePendingSubscription($subscription, $user),
            false => $this->subscriptions->deactivateActiveSubscription($subscription, $user),
        };
    }
}
