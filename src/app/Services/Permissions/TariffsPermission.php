<?php

namespace App\Services\Permissions;

class TariffsPermission extends Permission
{

    public const MANAGE = 'manage_tariffs';
    public const CREATE = 'create_tariffs';
    public const READ = 'read_tariffs';
    public const UPDATE = 'update_tariffs';
    public const DELETE = 'delete_tariffs';
    public const RESTORE = 'restore_tariffs';

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
            self::MANAGE => 'Управлять тарифами',
            self::CREATE => 'Создавать тарифы',
            self::READ => 'Просматривать тарифы',
            self::UPDATE => 'Обновлять тарифы',
            self::DELETE => 'Удалять тарифы',
            self::RESTORE => 'Восстанавливать удаленные тарифы',
        ];
    }
}