<?php
/**
 * File: UserService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-21
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\User;

use App\Events\UserCreatedEvent;
use App\Models\User;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use App\Services\User\Exceptions\OldPasswordInvalidException;

/**
 * Class UserService
 * @package App\Services\User
 */
class UserService
{
    private UserRepository $userRepository;
    private PersonRepository $personRepository;

    public function __construct(UserRepository $userRepository, PersonRepository $personRepository)
    {
        $this->userRepository = $userRepository;
        $this->personRepository = $personRepository;
    }

    public function createUser(
        \App\Http\Requests\ManagerApi\DTO\StoreUser $userDto,
        \App\Http\Requests\DTO\StorePerson $personDto
    ): User {
        $user = \DB::transaction(function () use ($userDto, $personDto) {
            $person = $this->personRepository->createFromDto($personDto);
            return $this->userRepository->createFromPerson($person, $userDto);
        });

        event(new UserCreatedEvent($user));

        return $user;
    }

    public function createUserForExistingPerson(
        \App\Models\Person $person,
        \App\Http\Requests\ManagerApi\DTO\StoreUser $userDto
    ): User {
        $user = $this->userRepository->createFromPerson($person, $userDto);

        event(new UserCreatedEvent($user));

        return $user;
    }

    public function update(User $user, \App\Http\Requests\ManagerApi\DTO\UpdateUser $updateUserDto): void
    {
        $this->userRepository->update($user, $updateUserDto);
    }

    public function updatePassword(User $user, \App\Http\Requests\ManagerApi\DTO\UpdateUserPassword $dto): void
    {
        if (!\Hash::check($dto->old_password, $user->password)) {
            throw new OldPasswordInvalidException();
        }

        $this->userRepository->updatePassword($user, $dto->new_password);
    }

    public function delete(User $user): void
    {
        $this->userRepository->delete($user);
    }
}
