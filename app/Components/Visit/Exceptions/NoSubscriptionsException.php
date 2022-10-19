<?php

namespace App\Components\Visit\Exceptions;

use App\Components\Subscription\Formatter;
use App\Components\Visit\Entity\PriceOptions;
use App\Exceptions\UserAssistanceRequiredException;
use Illuminate\Support\Collection;

class NoSubscriptionsException extends UserAssistanceRequiredException
{
    public function __construct(
        public readonly PriceOptions $priceOptions,
        public Collection $subscriptions,
    ) {
        parent::__construct([
            'payment_options' => $this->priceOptions->toArray(),
            'subscriptions' => Formatter::collection($subscriptions)->toArray(\request()),
        ], 'no_subscriptions');
    }
}