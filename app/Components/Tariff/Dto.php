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
    public ?int $courses_count;
    public ?int $visits_count;
    public ?int $days_count;
    public ?int $holds_count;
    public TariffStatus $status;
}