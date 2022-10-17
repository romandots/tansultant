<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Models\Enum\SubscriptionStatus;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $tariff_id;
    public string $student_id;
    public ?string $bonus_id = null;
    public string $name;
    public SubscriptionStatus $status;
    public ?int $days_limit = null;
    public ?int $courses_limit = null;
    public ?int $visits_limit = null;
    public ?int $holds_limit = null;
}