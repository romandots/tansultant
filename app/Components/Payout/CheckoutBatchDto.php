<?php

namespace App\Components\Payout;

use App\Common\DTO\DtoWithUser;

/**
 * @property string[] $ids
 */
class CheckoutBatchDto extends DtoWithUser
{
    public array $ids;
    public string $account_id;
}