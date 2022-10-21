<?php

namespace App\Components\Visit\Exceptions;

use App\Components\Visit\Entity\PriceOptions;
use App\Exceptions\UserAssistanceRequiredException;

class ChooseSubscriptionException extends UserAssistanceRequiredException
{
    public function __construct(public readonly PriceOptions $priceOptions)
    {
        parent::__construct($this->priceOptions->toArray(), 'choose_subscription');
    }
}