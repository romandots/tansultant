<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Components\Loader;
use App\Models\User;
use App\Services\Notification\Providers\SmsProvider;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SmsTest extends Command
{
    protected $signature = 'sms:test';
    protected $description = 'Test SMS sending';

    public function handle(): void
    {
        $phoneNumber = $this->ask('Phone number');

        $this->info('Testing direct SMS send');

        /** @var SmsProvider $smsProvider */
        $smsProvider = app(SmsProvider::class);
        $smsProvider->to($phoneNumber)->send('This is a test SMS from the system. Have a nice day!');

        $this->info('SMS sent');

        $person = Loader::people()->getByPhoneNumber($phoneNumber);
        if (null !== $person) {
            $this->info('Found a person with this phone number: ' . $person->name);
            $this->info('Testing person notification via SMS');

            Loader::notifications()->notify(
                $person,
                "Hey {$person->name}, this is a test SMS from the system. Have a nice day!"
            );
            $this->info('SMS sent');
            return;
        }
    }

    /**
     * @param User $user
     * @return void
     */
    protected function assignAdminRole(User $user): void
    {
        $guardName = \config('permission.guard_name', 'api');
        $adminRole = Role::findByName(\App\Services\Permissions\UserRoles::ADMIN, $guardName);
        $user->assignRole($adminRole);
    }
}
