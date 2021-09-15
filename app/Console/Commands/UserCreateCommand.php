<?php

namespace App\Console\Commands;

use App\Models\Person;

class UserCreateCommand extends UserCommand
{
    protected $signature = 'user:create {username} {password}';
    protected $description = 'Creates user with password';

    public function handle(): void
    {
        $userDto = new \App\Http\Requests\ManagerApi\DTO\StoreUser();
        $personDto = new \App\Http\Requests\DTO\StorePerson();

        $userDto->username = $this->argument('username');
        $userDto->password = $this->argument('password');

        $personDto->last_name = $this->ask('Last name');
        $personDto->first_name = $this->ask('First name');
        $personDto->patronymic_name = $this->ask('Patronymic name');
        $personDto->phone = $this->ask('Phone number');
        $personDto->email = $this->ask('Email');
        $personDto->gender = $this->choice('Gender', Person::GENDER);

        $birthDate = $this->ask('Birth date');
        $personDto->birth_date = $birthDate ? \Carbon\Carbon::parse($birthDate) : null;

        $user = $this->userService->createUser($userDto, $personDto);

        $this->info(
            "User #{$user->id} with password '{$userDto->password}' created in status [{$user->status}]"
        );
    }
}
