<?php
/**
 * File: SchedulesPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-24
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class SchedulesPermissions
 * @package App\Services\Permissions
 */
class SchedulesPermissions extends Permissions
{
    public const MANAGE = 'manage_schedules';
    public const CREATE = 'create_schedules';
    public const READ = 'read_schedules';
    public const UPDATE = 'update_schedules';
    public const DELETE = 'delete_schedules';
    public const RESTORE = 'restore_schedules';

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
            self::MANAGE => 'Управлять расписаниями',
            self::CREATE => 'Создавать расписания',
            self::READ => 'Просматривать расписания',
            self::UPDATE => 'Обновлять расписания',
            self::DELETE => 'Удалять расписания',
            self::RESTORE => 'Восстанавливать удаленные расписания',
        ];
    }
}
