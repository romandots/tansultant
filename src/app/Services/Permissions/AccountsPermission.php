<?php

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class AccountsPermissions
 * @package App\Services\Permissions
 */
class AccountsPermission extends Permission
{
    public const MANAGE = 'manage_accounts';
    public const CREATE = 'create_accounts';
    public const READ = 'read_accounts';
    public const UPDATE = 'update_accounts';
    public const DELETE = 'delete_accounts';
    public const RESTORE = 'restore_accounts';

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
            self::MANAGE => 'Управлять счетами',
            self::CREATE => 'Создавать счета',
            self::READ => 'Просматривать счета',
            self::UPDATE => 'Обновлять счета',
            self::DELETE => 'Удалять счета',
            self::RESTORE => 'Восстанавливать удаленные счета',
        ];
    }
}
