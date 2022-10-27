<?php

declare(strict_types=1);

namespace App\Events\Hold;

use App\Events\BaseModelEvent;
use App\Models\Enum\LogRecordObjectType;
use App\Models\Hold;
use App\Models\User;

abstract class HoldEvent extends BaseModelEvent
{
    public function __construct(
        public readonly Hold $hold,
        public readonly User $user,
    ) {
    }

    public function getType(): LogRecordObjectType
    {
        return LogRecordObjectType::HOLD;
    }

    public function getRecordId(): string
    {
        return $this->hold->id;
    }

    public static function created(Hold $hold, User $user): void
    {
        HoldCreatedEvent::dispatch($hold, $user);
    }

    public static function ended(Hold $hold, User $user): void
    {
        HoldEndedEvent::dispatch($hold, $user);
    }

    public static function deleted(Hold $hold, User $user): void
    {
        HoldDeletedEvent::dispatch($hold, $user);
    }
}