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

/**
 * Trait CreatesFakeUser
 * @package Tests\Traits
 */
trait CreatesFakeUser
{
    /**
     * @param array|null $attributes
     * @param array $permissions
     * @return \App\Models\User
     */
    private function createFakeUser(array $attributes = [], array $permissions = []): \App\Models\User
    {
        if (!isset($attributes['person_id'])) {
            $person = $this->createFakePerson();
            $attributes['person_id'] = $person->id;
        }

        /** @var User $user */
        $user = \factory(\App\Models\User::class)->create($attributes);
        $user->givePermissionTo($permissions);

        return $user;
    }
}
