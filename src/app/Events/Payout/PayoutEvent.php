<?php

namespace App\Events\Payout;

use App\Models\Payout;
use App\Models\User;

class PayoutEvent extends \App\Events\BaseEvent
{
    public function __construct(
        public readonly Payout $payout,
        public readonly User $user,
    ) {
    }

    public function getChannelName(): string
    {
        return \sprintf('%s.%s', 'payout', $this->payout->id);
    }

    public static function checkedOut(Payout $payout, User $user): void
    {
        PayoutCheckedOutEvent::dispatch($payout, $user);
    }

    public static function reportGenerated(Payout $payout, User $user): void
    {
        PayoutReportGeneratedEvent::dispatch($payout, $user);
    }
}