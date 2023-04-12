<?php

namespace App\Components\Shift\Exceptions;

class UserHasNoActiveShift extends Exception
{
    public function __construct(
        public readonly \App\Models\User $user
    ) {
        parent::__construct('user_has_no_active_shift', ['user' => $user->name], 403);
    }
}