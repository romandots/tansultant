<?php

namespace App\Console\Commands;

use App\Models\Enum\UserStatus;

class UserStatusUpdateCommand extends UserCommand
{
    protected $signature = 'user:status {username}';
    protected $description = 'Change user status';

    public function handle(): void
    {
        $username = $this->argument('username');
        $user = $this->users->findByUsername($username);

        $this->info("User {$username}'s current status is {$user->status->value}");

        $user->status = UserStatus::from($this->choice('Choose new status', \enum_strings(UserStatus::class)));
        $user->save();

        $this->info("User {$username}'s current status is {$user->status->value}");
    }
}
