<?php

namespace App\Console\Commands;

use App\Components\Loader;
use Illuminate\Console\Command;

abstract class UserCommand extends Command
{
    protected \App\Components\User\Facade $users;

    public function __construct()
    {
        parent::__construct();
        $this->users = Loader::users();
    }
}
