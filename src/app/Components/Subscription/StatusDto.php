<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Models\Enum\SubscriptionStatus;

class StatusDto extends \App\Common\DTO\StatusDto
{
    public SubscriptionStatus $status;
}