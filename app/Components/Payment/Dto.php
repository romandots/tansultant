<?php

declare(strict_types=1);

namespace App\Components\Payment;

use App\Models\Enum\PaymentObjectType;
use App\Models\Enum\PaymentStatus;
use App\Models\Enum\PaymentTransferType;
use App\Models\Enum\PaymentType;

class Dto extends \App\Common\DTO\DtoWIthUser
{
    public ?string $id;

    public int $amount;

    public string $name;

    public ?string $account_id;

    public ?string $object_id;

    public PaymentObjectType $object_type;

    public PaymentType $type;

    public PaymentTransferType $transfer_type;

    public PaymentStatus $status;

    public ?string $external_id = null;

    public string $user_id;

    public ?\Carbon\Carbon $confirmed_at;
}