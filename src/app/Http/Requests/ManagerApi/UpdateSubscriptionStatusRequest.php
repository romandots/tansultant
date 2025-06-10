<?php
/**
 * File: StorePersonRequest.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Requests\ManagerApi;

use App\Components\Subscription\StatusDto;
use App\Models\Enum\SubscriptionStatus;

class UpdateSubscriptionStatusRequest extends UpdateStatusRequest
{
    protected function getDtoClass(): string
    {
        return StatusDto::class;
    }

    protected function getStatusEnum(): string
    {
        return SubscriptionStatus::class;
    }
}
