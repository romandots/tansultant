<?php
/**
 * File: UserRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\ManagerApi\DTO\StoreUser;
use App\Http\Requests\ManagerApi\DTO\UpdateUser;
use App\Models\Person;
use App\Models\User;
use Carbon\Carbon;

/**
 * Class UserRepository
 * @package App\Repository
 */
class UserRepository
{
    /**
     * @param string $username
     * @return User|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByUsername(string $username): ?User
    {
        return User::query()
            ->where('username', $username)
            ->firstOrFail();
    }

    /**
     * @param string $id
     * @return User|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): ?User
    {
        return User::query()
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->firstOrFail();
    }

    /**
     * @param string $id
     * @return User|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findWithDeleted(string $id): ?User
    {
        return User::query()
            ->where('id', $id)
            ->firstOrFail();
    }

    /**
     * @param Person $person
     * @param StoreUser $dto
     * @return User
     * @throws \Exception
     */
    public function createFromPerson(Person $person, StoreUser $dto): User
    {
        $user = new User();
        $user->id = \uuid();
        $user->created_at = Carbon::now();
        $user->updated_at = Carbon::now();
        $user->person_id = $person->id;
        $user->name = \trans('person.user_name', $person->compactName());
        $user->username = $dto->username;
        if ($dto->password) {
            $user->password = \Hash::make($dto->password);
        }
        $user->save();

        return $user;
    }

    /**
     * @param User $user
     * @param UpdateUser $dto
     */
    public function update(User $user, UpdateUser $dto): void
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

        $user->updated_at = Carbon::now();
        $user->save();
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function updatePassword(User $user, string $password): void
    {
        $user->password = \Hash::make($password);
        $user->updated_at = Carbon::now();
        $user->save();
    }

    /**
     * @param User $user
     */
    public function updateSeenAt(User $user): void
    {
        $user->seen_at = Carbon::now();
        $user->updated_at = Carbon::now();
        $user->save();
    }

    /**
     * @param User $user
     */
    public function approve(User $user): void
    {
        $user->approved_at = Carbon::now();
        $user->updated_at = Carbon::now();
        $user->save();
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function delete(User $user): void
    {
        $user->deleted_at = Carbon::now();
        $user->updated_at = Carbon::now();
        $user->save();
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function restore(User $user): void
    {
        $user->deleted_at = null;
        $user->updated_at = Carbon::now();
        $user->save();
    }
}
