<?php
/**
 * File: ClassroomsPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-08-1
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class ClassroomsPermissions
 * @package App\Services\Permissions
 */
class ClassroomsPermissions extends Permissions
{
    public const MANAGE = 'manage_classrooms';
    public const CREATE = 'create_classrooms';
    public const READ = 'read_classrooms';
    public const UPDATE = 'update_classrooms';
    public const DELETE = 'delete_classrooms';
    public const RESTORE = 'restore_classrooms';

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
            self::MANAGE => 'Управлять залами',
            self::CREATE => 'Создавать залы',
            self::READ => 'Просматривать залы',
            self::UPDATE => 'Обновлять залы',
            self::DELETE => 'Удалять залы',
            self::RESTORE => 'Восстанавливать удаленные залы',
        ];
    }
}
