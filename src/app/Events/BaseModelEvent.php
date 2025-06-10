<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Enum\LogRecordObjectType;
use JetBrains\PhpStorm\Pure;

/**
 * @package App\Events
 */
abstract class BaseModelEvent extends BaseEvent
{

    abstract public function getType(): LogRecordObjectType;

    abstract public function getRecordId(): string;

    #[Pure] public function getChannelName(): string
    {
        return sprintf('event.%s.%s', $this->getType()?->value, $this->getRecordId());
    }
}
