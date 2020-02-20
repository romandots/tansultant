<?php
declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @package App\Events
 */
class BaseEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public const CHANNEL_NAME = 'events';

    /**
     * Get the channels the event should broadcast on.
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(self::CHANNEL_NAME);
    }
}
