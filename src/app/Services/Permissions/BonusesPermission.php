<?php

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class AccountsPermissions
 * @package App\Services\Permissions
 */
class BonusesPermission extends Permission
{
    public const MANAGE = 'manage_bonuses';
    public const CREATE = 'create_bonuses';
    public const READ = 'read_bonuses';
    public const UPDATE = 'update_bonuses';
    public const DELETE = 'delete_bonuses';
    public const RESTORE = 'restore_bonuses';

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
            self::MANAGE => 'Управлять бонусами',
            self::CREATE => 'Создавать бонусы',
            self::READ => 'Просматривать бонусы',
            self::UPDATE => 'Обновлять бонусы',
            self::DELETE => 'Удалять бонусы',
            self::RESTORE => 'Восстанавливать удаленные бонусы',
        ];
    }
}
