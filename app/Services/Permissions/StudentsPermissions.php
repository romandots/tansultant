<?php
/**
 * File: StudentsPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class StudentsPermissions
 * @package App\Services\Permissions
 */
class StudentsPermissions
{
    public const MANAGE_STUDENTS = 'manage_students';
    public const CREATE_STUDENTS = 'create_students';
    public const READ_STUDENTS = 'read_students';
    public const UPDATE_STUDENTS = 'update_students';
    public const DELETE_STUDENTS = 'delete_students';

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
            self::MANAGE_STUDENTS => 'Управлять студентами',
            self::CREATE_STUDENTS => 'Создавать студентов',
            self::READ_STUDENTS => 'Просматривать студентов',
            self::UPDATE_STUDENTS => 'Обновлять студентов',
            self::DELETE_STUDENTS => 'Удалять студентов',
        ];
    }
}
