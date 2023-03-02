<?php

declare(strict_types=1);

namespace App\Components\Payout;

use App\Models\Enum\PayoutStatus;
use Carbon\Carbon;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public string $branch_id;
    public string $instructor_id;
    public Carbon $period_from;
    public Carbon $period_to;
    public PayoutStatus $status;
}