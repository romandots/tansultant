<?php
/**
 * File: CreatesFakeUser.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\User;
use App\Services\Permissions\UserRoles;

/**
 * Trait CreatesFakeUser
 * @package Tests\Traits
 */
trait CreatesFakeUser
{
    /**
     * @param array|null $attributes
     * @param array $permissions
     * @param array $roles
     * @return \App\Models\User
     */
    protected function createFakeUser(
        array $attributes = [],
        array $permissions = [],
        array $roles = []
    ): \App\Models\User {
        if (!isset($attributes['person_id'])) {
            $person = $this->createFakePerson();
            $attributes['person_id'] = $person->id;
        }

        /** @var User $user */
        $user = \App\Models\User::factory()->create($attributes);
        $user->givePermissionTo($permissions);

        $user->assignRole($roles);

        return $user;
    }

    /**
     * @param array|null $attributes
     * @param array $permissions
     * @return \App\Models\User
     */
    protected function createFakeManagerUser(array $attributes = [], array $permissions = []): \App\Models\User
    {
        /** @var User $user */
        $user = $this->createFakeUser($attributes, $permissions);
        $user->assignRole(UserRoles::MANAGER);

        return $user;
    }
}
