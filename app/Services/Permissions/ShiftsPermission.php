<?php

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class ShiftsPermissions
 * @package App\Services\Permissions
 */
class ShiftsPermission extends Permission
{
    public const MANAGE = 'manage_shifts';
    public const CREATE = 'create_shifts';
    public const READ = 'read_shifts';
    public const UPDATE = 'update_shifts';
    public const DELETE = 'delete_shifts';
    public const RESTORE = 'restore_shifts';

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
            self::MANAGE => 'Управлять сменами',
            self::CREATE => 'Создавать смены',
            self::READ => 'Просматривать смены',
            self::UPDATE => 'Обновлять смены',
            self::DELETE => 'Удалять смены',
            self::RESTORE => 'Восстанавливать удаленные смены',
        ];
    }
}
