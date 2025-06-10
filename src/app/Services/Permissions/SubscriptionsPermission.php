<?php

namespace App\Services\Permissions;

class SubscriptionsPermission extends Permission
{
    public const MANAGE = 'manage_subscriptions';
    public const CREATE = 'create_subscriptions';
    public const READ = 'read_subscriptions';
    public const UPDATE = 'update_subscriptions';
    public const DELETE = 'delete_subscriptions';
    public const RESTORE = 'restore_subscriptions';

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
            self::MANAGE => 'Управлять подписками',
            self::CREATE => 'Создавать подписки',
            self::READ => 'Просматривать подписки',
            self::UPDATE => 'Обновлять подписки',
            self::DELETE => 'Удалять подписки',
            self::RESTORE => 'Восстанавливать удаленные подписки',
        ];
    }
}