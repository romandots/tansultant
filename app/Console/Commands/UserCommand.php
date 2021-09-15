<?php

namespace App\Console\Commands;

use App\Services\User\UserService;
use Illuminate\Console\Command;

abstract class UserCommand extends Command
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }
}
