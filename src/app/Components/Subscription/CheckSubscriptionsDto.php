<?php

declare(strict_types=1);

namespace App\Components\Subscription;

class CheckSubscriptionsDto extends \App\Common\DTO\DtoWithUser
{
    public string $student_id;
    /** @var string[] */
    public array $lessons_ids = [];
}