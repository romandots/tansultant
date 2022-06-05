<?php
declare(strict_types=1);

namespace App\Events\User;

use App\Events\BaseEvent;
use App\Models\User;
use JetBrains\PhpStorm\Pure;

abstract class UserEvent extends BaseEvent
{
    public function __construct(
        protected User $user,
    ) { }

    #[Pure] public function getChannelName(): string
    {
        return 'user.' . $this->getUserId();
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->user->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public static function created(User $user): void
    {
        (new UserCreatedEvent($user))::dispatch();
    }

    public static function registered(User $user): void
    {
        (new UserRegisteredEvent($user))::dispatch();
    }
}
