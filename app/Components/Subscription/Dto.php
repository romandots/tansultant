<?php

declare(strict_types=1);

namespace App\Components\Subscription;

use App\Models\Enum\SubscriptionStatus;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $tariff_id;
    public string $student_id;
    public string $name;
    public SubscriptionStatus $status;
    public ?int $days_count = null;
    public ?int $courses_count = null;
    public ?int $visits_count = null;
    public ?int $holds_count = null;
}