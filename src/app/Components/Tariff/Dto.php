<?php

declare(strict_types=1);

namespace App\Components\Tariff;

use App\Models\Enum\TariffStatus;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public float $price;
    public float $prolongation_price;
    public ?int $courses_limit;
    public ?int $visits_limit;
    public ?int $days_limit;
    public ?int $holds_limit;
    public TariffStatus $status;
}