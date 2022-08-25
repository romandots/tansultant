<?php

namespace App\Components\Visit\Exceptions;

use App\Exceptions\UserAssistanceRequiredException;

class NoSubscriptionsException extends UserAssistanceRequiredException
{

    /**
     * @param int $price
     */
    public function __construct(int $price)
    {
        parent::__construct(['price' => $price], 'no_subscriptions', 409);
    }
}