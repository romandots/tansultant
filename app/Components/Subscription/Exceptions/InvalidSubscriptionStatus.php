<?php

namespace App\Components\Subscription\Exceptions;

use App\Exceptions\InvalidStatusException;

class InvalidSubscriptionStatus extends InvalidStatusException
{
    public function __construct(string $status, array $allowed, string $message = 'invalid_subscription_status')
    {
        parent::__construct($status, $allowed, $message);
    }
}