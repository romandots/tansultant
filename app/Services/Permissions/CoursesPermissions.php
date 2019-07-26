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
class CoursesPermissions
{
    public const MANAGE_COURSES = 'manage_courses';
    public const CREATE_COURSES = 'create_courses';
    public const READ_COURSES = 'read_courses';
    public const UPDATE_COURSES = 'update_courses';
    public const DELETE_COURSES = 'delete_courses';

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
            self::MANAGE_COURSES => 'Управлять курсами',
            self::CREATE_COURSES => 'Создавать курсы',
            self::READ_COURSES => 'Просматривать курсы',
            self::UPDATE_COURSES => 'Обновлять курсы',
            self::DELETE_COURSES => 'Удалять курсы',
        ];
    }
}
