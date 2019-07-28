<?php
/**
 * File: VisitService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Visit;

use App\Models\Visit;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class VisitService
 * @package App\Services\Visit
 */
class VisitService
{
    /**
     * @param Collection|Visit[] $visits
     * @return bool
     */
    public function visitsArePaid(Collection $visits): bool
    {
        foreach ($visits as $visit) {
            if ($visit->payment_type !== '\App\Models\Payment'
                || null === $visit->payment
                || null === $visit->payment->paid_at) {
                return false;
            }
        }

        return true;
    }
}
