<?php

namespace App\Console\Commands;

use App\Components\Loader;
use App\Components\Person\Exceptions\PersonAlreadyExist;
use App\Models\Enum\Gender;

class UserCreateCommand extends UserCommand
{
    protected $signature = 'user:create {username} {password}';
    protected $description = 'Creates user with password';

    public function handle(): void
    {
        $userDto = new \App\Components\User\Dto();
        $personDto = new \App\Components\Person\Dto();

        $userDto->username = $this->argument('username');
        $userDto->password = $this->argument('password');

        $personDto->last_name = utf8_encode($this->ask('Last name'));
        $personDto->first_name = utf8_encode($this->ask('First name'));
        $personDto->patronymic_name = utf8_encode($this->ask('Patronymic name'));
        $personDto->phone = $this->ask('Phone number');
        $personDto->email = $this->ask('Email');
        $personDto->gender = Gender::tryFrom(
            $this->choice('Gender', \enum_strings(Gender::class))
        );

        $birthDate = $this->ask('Birth date');
        $personDto->birth_date = $birthDate ? \Carbon\Carbon::parse($birthDate) : null;

        try {
            $person = Loader::people()->create($personDto);
        } catch (PersonAlreadyExist $e) {
            $person = $e->getPerson();
        }

        if (null === $person) {
            $this->error('Person was not created');
            return;
        }

        $userDto->person_id = $person->id;
        $user = $this->users->createFromPerson($userDto);

        $this->info(
            "User #{$user->id} <{$user->name}> with password '{$userDto->password}' created in status [{$user->status->value}]"
        );
    }
}
