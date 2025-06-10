<?php

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class CreditsPermissions
 * @package App\Services\Permissions
 */
class CreditsPermission extends Permission
{
    public const MANAGE = 'manage_credits';
    public const CREATE = 'create_credits';
    public const READ = 'read_credits';
    public const UPDATE = 'update_credits';
    public const DELETE = 'delete_credits';

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
            self::MANAGE => 'Управлять кредитными средствами',
            self::CREATE => 'Создавать кредитные средства',
            self::READ => 'Просматривать кредитные средства',
            self::UPDATE => 'Обновлять кредитные средства',
            self::DELETE => 'Удалять кредитные средства',
        ];
    }
}
