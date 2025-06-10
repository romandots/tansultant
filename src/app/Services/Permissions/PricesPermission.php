<?php

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class PricesPermissions
 * @package App\Services\Permissions
 */
class PricesPermission extends Permission
{
    public const MANAGE = 'manage_prices';
    public const CREATE = 'create_prices';
    public const READ = 'read_prices';
    public const UPDATE = 'update_prices';
    public const DELETE = 'delete_prices';
    public const RESTORE = 'restore_prices';

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
            self::MANAGE => 'Управлять ценами',
            self::CREATE => 'Создавать цены',
            self::READ => 'Просматривать цены',
            self::UPDATE => 'Обновлять цены',
            self::DELETE => 'Удалять цены',
            self::RESTORE => 'Восстанавливать удаленные цены',
        ];
    }
}
