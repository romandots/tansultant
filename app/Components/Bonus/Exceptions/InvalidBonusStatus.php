<?php

namespace App\Components\Bonus\Exceptions;

use App\Exceptions\InvalidStatusException;

class InvalidBonusStatus extends InvalidStatusException
{
    public function __construct(object $currentStatus, array $allowedStatuses = [], string $message = 'invalid_bonus_status')
    {
        parent::__construct($currentStatus->value, $allowedStatuses, $message);
    }
}