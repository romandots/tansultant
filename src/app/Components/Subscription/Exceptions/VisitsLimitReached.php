<?php

namespace App\Components\Subscription\Exceptions;

class VisitsLimitReached extends Exception
{
    public function __construct(int $limit)
    {
        parent::__construct('visits_limit_reached', [
            'limit' => $limit,
        ], 409);
    }
}