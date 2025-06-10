<?php

declare(strict_types=1);

namespace App\Components\Hold;

use Carbon\Carbon;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $subscription_id;
    public Carbon $starts_at;
}