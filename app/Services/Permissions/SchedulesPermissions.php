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
class SchedulesPermissions
{
    public const MANAGE_SCHEDULES = 'manage_schedules';
    public const CREATE_SCHEDULES = 'create_schedules';
    public const READ_SCHEDULES = 'read_schedules';
    public const UPDATE_SCHEDULES = 'update_schedules';
    public const DELETE_SCHEDULES = 'delete_schedules';

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
            self::MANAGE_SCHEDULES => 'Управлять расписаниями',
            self::CREATE_SCHEDULES => 'Создавать расписания',
            self::READ_SCHEDULES => 'Просматривать расписания',
            self::UPDATE_SCHEDULES => 'Обновлять расписания',
            self::DELETE_SCHEDULES => 'Удалять расписания',
        ];
    }
}
