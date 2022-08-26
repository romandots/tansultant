<?php

namespace App\Components\Subscription\Exceptions;

use App\Exceptions\BaseException;

class ProlongationPeriodExpired extends BaseException
{
    public function __construct(\Carbon\Carbon $expiredAt, int $prolongationPeriod)
    {
        parent::__construct('prolongation_period_expired', [
            'expired_at' => $expiredAt,
            'prolong_until' => $expiredAt->addDays($prolongationPeriod),
        ], 409);
    }
}