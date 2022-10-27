<?php

namespace App\Events\Visit;

use App\Models\Enum\LogRecordObjectType;
use App\Models\User;
use App\Models\Visit;

class VisitEvent extends \App\Events\BaseEvent
{
    public function __construct(
        public readonly Visit $visit,
        public readonly User $user,
    ) {
    }

    public function getChannelName(): string
    {
        return \sprintf('%s.%s', 'visit', $this->getRecordId());
    }

    public function getType(): LogRecordObjectType
    {
        return LogRecordObjectType::VISIT;
    }

    public function getRecordId(): string
    {
        return $this->visit->id;
    }

    public static function created(Visit $visit, User $user): void
    {
        VisitCreatedEvent::dispatch($visit, $user);
    }

    public static function deleted(Visit $visit, User $user): void
    {
        VisitDeletedEvent::dispatch($visit, $user);
    }
}