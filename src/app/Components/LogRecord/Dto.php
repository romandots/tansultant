<?php

declare(strict_types=1);

namespace App\Components\LogRecord;

use App\Models\Enum\LogRecordAction;
use App\Models\Enum\LogRecordObjectType;

class Dto extends \App\Common\DTO\DtoWithUser
{
    public ?string $id;
    public string $name;
    public LogRecordAction $action;
    public LogRecordObjectType $object_type;
    public ?string $object_id = null;
    public ?string $message = null;
    public mixed $old_value = null;
    public mixed $new_value = null;
}