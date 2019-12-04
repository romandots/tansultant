<?php
/**
 * File: PersonsPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class PersonsPermissions
 * @package App\Services\Permissions
 */
class PersonsPermissions
{
    public const MANAGE = 'manage_persons';
    public const CREATE = 'create_persons';
    public const READ = 'read_persons';
    public const UPDATE = 'update_persons';
    public const DELETE = 'delete_persons';
    public const RESTORE = 'restore_persons';

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
            self::MANAGE => 'Управлять профилями',
            self::CREATE => 'Создавать профили',
            self::READ => 'Просматривать профили',
            self::UPDATE => 'Обновлять профили',
            self::DELETE => 'Удалять профили',
            self::RESTORE => 'Восстанавливать удаленные профили',
        ];
    }
}
