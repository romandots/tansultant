<?php

namespace App\Components\Payout;

use App\Common\DTO\DtoWithUser;

/**
 * @property string[] $ids
 */
class UpdateBatchDto extends DtoWithUser
{
    public array $ids;
}