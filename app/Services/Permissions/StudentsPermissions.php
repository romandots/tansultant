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
class StudentsPermissions extends Permissions
{
    public const MANAGE = 'manage_students';
    public const CREATE = 'create_students';
    public const READ = 'read_students';
    public const UPDATE = 'update_students';
    public const DELETE = 'delete_students';
    public const RESTORE = 'restore_students';

    /**
     * Get names of all defined permissions
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
     * @return string[]
     */
    public static function getInitialDescriptions(): array
    {
        return [
            self::MANAGE => 'Управлять студентами',
            self::CREATE => 'Создавать студентов',
            self::READ => 'Просматривать студентов',
            self::UPDATE => 'Обновлять студентов',
            self::DELETE => 'Удалять студентов',
            self::RESTORE => 'Восстанавливать удаленных студентов',
        ];
    }
}
