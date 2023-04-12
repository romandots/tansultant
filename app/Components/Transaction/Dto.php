<?php

declare(strict_types=1);

namespace App\Components\Transaction;

use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionTransferType;
use App\Models\Enum\TransactionType;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;

    public int $amount;

    public ?string $name = null;

    public ?string $account_id;

    public ?string $customer_id = null;

    public ?string $shift_id = null;

    public ?TransactionType $type;

    public ?TransactionTransferType $transfer_type;

    public ?TransactionStatus $status;

    public ?string $external_id = null;

    public ?string $user_id;

    public ?\Carbon\Carbon $confirmed_at;
}