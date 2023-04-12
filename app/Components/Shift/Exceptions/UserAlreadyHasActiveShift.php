<?php

namespace App\Components\Shift\Exceptions;

class UserAlreadyHasActiveShift extends Exception
{
    public function __construct(
        public readonly \App\Models\User $user
    ) {
        parent::__construct('user_already_has_active_shift', ['user' => $user->name], 403);
    }
}