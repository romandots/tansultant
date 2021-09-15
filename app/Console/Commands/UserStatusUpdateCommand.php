<?php

namespace App\Console\Commands;

use App\Models\Person;
use App\Models\User;

class UserStatusUpdateCommand extends UserCommand
{
    protected $signature = 'user:status {username}';
    protected $description = 'Change user status';

    public function handle(): void
    {
        $repo = $this->userService->getUserRepository();

        $username = $this->argument('username');
        $user = $repo->findByUsername($username);

        $this->info("User {$username}'s current status is {$user->status}");

        $user->status = $this->choice('Choose new status', User::STATUSES);
        $user->save();

        $this->info("User {$username}'s current status is {$user->status}");
    }
}
