<?php

namespace App\Broadcasting;

class UserChannel
{
    public function join(\App\Models\User $user, string $userId): bool
    {
        return $user->id === $userId;
    }
}