<?php

declare(strict_types=1);

namespace App\Components\Bonus;

use App\Models\Enum\BonusStatus;
use App\Models\Enum\BonusType;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public int $amount;
    public BonusType $type;
    public BonusStatus $status;
    public string $account_id;
    public string $promocode_id;
    public string $user_id;
}