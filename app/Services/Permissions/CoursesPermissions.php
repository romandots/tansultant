<?php
/**
 * File: CoursesPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-23
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class CoursesPermissions
 * @package App\Services\Permissions
 */
class CoursesPermissions extends Permissions
{
    public const MANAGE = 'manage_courses';
    public const CREATE = 'create_courses';
    public const READ = 'read_courses';
    public const UPDATE = 'update_courses';
    public const DELETE = 'delete_courses';
    public const RESTORE = 'restore_courses';
    public const DISABLE = 'disable_courses';
    public const ENABLE = 'enable_courses';

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
            self::MANAGE => 'Управлять курсами',
            self::CREATE => 'Создавать курсы',
            self::READ => 'Просматривать курсы',
            self::UPDATE => 'Обновлять курсы',
            self::DELETE => 'Удалять курсы',
            self::RESTORE => 'Восстанавливать удаленные курсы',
            self::DISABLE => 'Отключать курсы',
            self::ENABLE => 'Включать отключенные курсы',
        ];
    }
}
