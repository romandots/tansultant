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
    public const MANAGE_VISITS = 'manage_visits';
    public const CREATE_VISITS = 'create_visits';
    public const READ_VISITS = 'read_visits';
    public const UPDATE_VISITS = 'update_visits';
    public const DELETE_VISITS = 'delete_visits';

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
            self::MANAGE_VISITS => 'Управлять посещениями',
            self::CREATE_VISITS => 'Создавать посещения',
            self::READ_VISITS => 'Просматривать посещения',
            self::UPDATE_VISITS => 'Обновлять посещения',
            self::DELETE_VISITS => 'Удалять посещения',
        ];
    }
}
