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
class ClassroomsPermissions
{
    public const MANAGE_CLASSROOMS = 'manage_classrooms';
    public const CREATE_CLASSROOMS = 'create_classrooms';
    public const READ_CLASSROOMS = 'read_classrooms';
    public const UPDATE_CLASSROOMS = 'update_classrooms';
    public const DELETE_CLASSROOMS = 'delete_classrooms';

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
            self::MANAGE_CLASSROOMS => 'Управлять залами',
            self::CREATE_CLASSROOMS => 'Создавать залы',
            self::READ_CLASSROOMS => 'Просматривать залы',
            self::UPDATE_CLASSROOMS => 'Обновлять залы',
            self::DELETE_CLASSROOMS => 'Удалять залы',
        ];
    }
}
