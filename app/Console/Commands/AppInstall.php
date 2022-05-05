<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Components\Loader;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class AppInstall extends Command
{
    public const ADMIN_USERNAME = 'admin';

    protected $signature = 'app:install';
    protected $description = 'Install app: seed permissions and create admin user';

    public function handle(): void
    {
        $this->info('Seeding all data');
        Artisan::call('db:seed');

        $password = $this->secret('Admin password');
        $confirmPassword = $this->secret('Confirm admin password');
        while ($password !== $confirmPassword) {
            $password = $this->secret('Admin password');
            $confirmPassword = $this->secret('Confirm admin password');
        }

        $existingUser = User::query()->where('username', self::ADMIN_USERNAME)->first();
        if (null !== $existingUser) {
            $this->info('Admin user already exists.');
            $existingUser->password = \Hash::make($password);
            $existingUser->save();
            $this->info('Password updated.');
            $this->assignAdminRole($existingUser);
            $this->info('Admin role set');
            return;
        }

        $personDto = new \App\Components\Person\Dto();
        $personDto->first_name = $this->ask('Admin first name');
        $personDto->last_name = $this->ask('Admin last name');
        $person = Loader::people()->create($personDto);

        $userDto = new \App\Components\User\Dto();
        $userDto->username = self::ADMIN_USERNAME;
        $userDto->password = $password ?? '12345678';
        $user = Loader::users()->createFromPerson($userDto, $person);
        Loader::users()->approve($user);
        $this->assignAdminRole($user);

        $this->info("User {$user->name} <{$user->username}> created!");
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
