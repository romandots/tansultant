<?php

namespace App\Components\Visit\Exceptions;

use App\Components\Loader;
use App\Components\Subscription\Formatter;
use App\Exceptions\UserAssistanceRequiredException;
use App\Models\Subscription;
use Illuminate\Support\Collection;

class ChooseSubscriptionException extends UserAssistanceRequiredException
{
    protected Collection $subscriptions;

    /**
     * @param Collection<Subscription> $subscriptions
     */
    public function __construct(Collection $subscriptions)
    {
        $this->subscriptions = $subscriptions;
        $formatted = $subscriptions
            ->map(fn ($subscription) => Loader::subscriptions()->format($subscription, Formatter::class))
            ->toArray();
        parent::__construct(['subscriptions' => $formatted], 'choose_subscription');
    }
}