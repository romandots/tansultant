<?php
declare(strict_types=1);

namespace App\Events\Shift;

use App\Components\Loader;
use App\Components\Shift\Formatter;
use App\Events\BaseEvent;
use App\Models\Shift;
use JetBrains\PhpStorm\Pure;

abstract class ShiftEvent extends BaseEvent
{
    public readonly array $shift;

    public function __construct(
        Shift $shift,
    ) {
        $shift->load('branch', 'user');
        $this->shift = Loader::shifts()->format($shift, Formatter::class);
    }

    #[Pure] public function getChannelName(): string
    {
        return 'shift.' . $this->getShiftId();
    }

    public function getShiftId(): ?string
    {
        return $this->shift['id'] ?? null;
    }

    public static function transactionsUpdated(Shift $shift): void
    {
        ShiftTransactionsUpdatedEvent::dispatch($shift);
    }
}
