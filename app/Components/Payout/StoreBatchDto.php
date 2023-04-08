<?php

declare(strict_types=1);

namespace App\Components\Payout;

use Carbon\Carbon;

class StoreBatchDto extends \App\Common\DTO\DtoWithUser
{
    public ?string $name;
    public string $branch_id;
    public Carbon $period_from;
    public Carbon $period_to;
}