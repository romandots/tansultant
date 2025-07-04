<?php
/**
 * File: InstructorPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class InstructorsPermissions
 * @package App\Services\Permissions
 */
class InstructorsPermission extends Permission
{
    public const MANAGE = 'manage_instructors';
    public const CREATE = 'create_instructors';
    public const READ = 'read_instructors';
    public const UPDATE = 'update_instructors';
    public const DELETE = 'delete_instructors';
    public const RESTORE = 'restore_instructors';

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
            self::MANAGE => 'Управлять инструкторами',
            self::CREATE => 'Создавать инструкторов',
            self::READ => 'Просматривать инструкторов',
            self::UPDATE => 'Обновлять инструкторов',
            self::DELETE => 'Удалять инструкторов',
            self::RESTORE => 'Восстанавливать удаленных инструкторов',
        ];
    }
}
