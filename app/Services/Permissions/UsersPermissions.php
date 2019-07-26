<?php
/**
 * File: UsersPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class UsersPermissions
 * @package App\Services\Permissions
 */
class UsersPermissions
{
    public const MANAGE_USERS = 'manage_users';
    public const CREATE_USERS = 'create_users';
    public const READ_USERS = 'read_users';
    public const UPDATE_USERS = 'update_users';
    public const DELETE_USERS = 'delete_users';

    /**
     * Get names of all defined permissions
     *
     * @return string[]
     * @throws \ReflectionException
     */
    public static function getAllNames(): array
    {
        $reflection = new \ReflectionClass(__CLASS__);

        return \array_values($reflection->getConstants());
    }

    /**
     * Get all built-in permission descriptions
     *
     * @return string[]
     */
    public static function getInitialDescriptions(): array
    {
        return [
            self::MANAGE_USERS => 'Управлять пользователями',
            self::CREATE_USERS => 'Создавать пользователей',
            self::READ_USERS => 'Просматривать пользователей',
            self::UPDATE_USERS => 'Обновлять пользователей',
            self::DELETE_USERS => 'Удалять пользователей',
        ];
    }
}
