<?php
/**
 * File: UserRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\Person;
use App\Models\User;

/**
 * Class UserRepository
 * @package App\Repository
 */
class UserRepository
{
    /**
     * @param int $id
     * @return User|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(int $id): ?User
    {
        return User::query()->findOrFail($id);
    }

    /**
     * @param Person $person
     * @param \App\Http\Requests\Api\DTO\User $dto
     * @return User
     */
    public function create(Person $person, \App\Http\Requests\Api\DTO\User $dto): User
    {
        $user = new User;
        $user->person_id = $person->id;
        $user->name = "{$person->last_name} {$person->first_name}";
        $user->username = $dto->username;
        if ($dto->password) {
            $user->password = \Hash::make($dto->password);
        }
        $user->save();

        return $user;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function delete(User $user): void
    {
        $user->delete();
    }

    /**
     * @param User $user
     * @param \App\Http\Requests\Api\DTO\UserUpdate $dto
     */
    public function update(User $user, \App\Http\Requests\Api\DTO\UserUpdate $dto): void
    {
        if ($dto->name) {
            $user->name = $dto->name;
        }

        if ($dto->username) {
            $user->username = $dto->username;
        }

        if ($dto->password) {
            $user->password = \Hash::make($dto->password);
        }

        $user->save();
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function updatePassword(User $user, string $password): void
    {
        $user->password = \Hash::make($password);
        $user->save();
    }
}
