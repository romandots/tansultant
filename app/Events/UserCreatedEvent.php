<?php
declare(strict_types=1);

namespace App\Events;

use App\Models\User;

class UserCreatedEvent extends BaseEvent
{
    public User $user;

    /**
     * Create a new event instance.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
