<?php

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class PaymentsPermissions
 * @package App\Services\Permissions
 */
class PaymentsPermission extends Permission
{
    public const MANAGE = 'manage_payments';
    public const CREATE = 'create_payments';
    public const READ = 'read_payments';
    public const UPDATE = 'update_payments';
    public const DELETE = 'delete_payments';
    public const RESTORE = 'restore_payments';

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
            self::MANAGE => 'Управлять пополнениями и списаниямами',
            self::CREATE => 'Создавать пополнения и списания',
            self::READ => 'Просматривать пополнения и списания',
            self::UPDATE => 'Обновлять пополнения и списания',
            self::DELETE => 'Удалять пополнения и списания',
            self::RESTORE => 'Восстанавливать удаленные пополнения и списания',
        ];
    }
}
