<?php

declare(strict_types=1);

namespace App\Components\Intent;

use App\Models\Enum\IntentEventType;
use App\Models\Enum\IntentStatus;

class Dto extends \App\Common\DTO\DtoWIthUser
{
    public ?string $id;

    public string $student_id;

    public string $manager_id;

    public string $event_id;

    public IntentEventType $event_type;

    public IntentStatus $status;
}