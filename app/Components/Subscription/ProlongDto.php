<?php

declare(strict_types=1);

namespace App\Components\Subscription;

class ProlongDto extends \App\Common\DTO\DtoWithUser
{
    public string $id;
    public ?string $bonus_id = null;
    public array $with = [];
    public array $with_count = [];
}