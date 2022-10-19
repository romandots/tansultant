<?php

declare(strict_types=1);

namespace App\Components\Hold;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $subscription_id;
}