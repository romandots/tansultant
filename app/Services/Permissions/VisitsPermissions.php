<?php
/**
 * File: VisitsPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class VisitsPermissions
 * @package App\Services\Permissions
 */
class VisitsPermissions
{
    public const MANAGE = 'manage_visits';
    public const CREATE = 'create_visits';
    public const READ = 'read_visits';
    public const UPDATE = 'update_visits';
    public const DELETE = 'delete_visits';
    public const RESTORE = 'restore_visits';

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
            self::MANAGE => 'Управлять посещениями',
            self::CREATE => 'Создавать посещения',
            self::READ => 'Просматривать посещения',
            self::DELETE => 'Удалять посещения',
            self::RESTORE => 'Восстанавливать удаленные посещения',
        ];
    }
}
