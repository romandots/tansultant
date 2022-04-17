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
class UsersPermissions extends Permissions
{
    public const MANAGE = 'manage_users';
    public const CREATE = 'create_users';
    public const READ = 'read_users';
    public const UPDATE = 'update_users';
    public const DELETE = 'delete_users';
    public const RESTORE = 'restore_users';

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
            self::MANAGE => 'Управлять пользователями',
            self::CREATE => 'Создавать пользователей',
            self::READ => 'Просматривать пользователей',
            self::UPDATE => 'Обновлять пользователей',
            self::DELETE => 'Удалять пользователей',
            self::RESTORE => 'Восстанавливать удаленных пользователей',
        ];
    }
}
