<?php
declare(strict_types=1);

namespace App\Events;

use App\Models\User;

class UserRegisteredEvent extends BaseEvent
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
