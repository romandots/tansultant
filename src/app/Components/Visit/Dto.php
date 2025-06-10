<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Models\Enum\VisitEventType;
use App\Models\Enum\VisitPaymentType;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $manager_id;
    public string $student_id;
    public string $event_id;
    public ?string $payment_id = null;
    public ?string $subscription_id = null;
    public ?string $bonus_id = null;
    public VisitEventType $event_type;
    public VisitPaymentType $payment_type;
    public bool $pay_from_balance;
    public ?int $price = null;
}