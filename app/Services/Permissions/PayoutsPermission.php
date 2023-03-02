<?php
/**
 * File: PayoutsPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class PayoutsPermissions
 * @package App\Services\Permissions
 */
class PayoutsPermission extends Permission
{
    public const MANAGE = 'manage_payouts';
    public const CREATE = 'create_payouts';
    public const READ = 'read_payouts';
    public const UPDATE = 'update_payouts';
    public const DELETE = 'delete_payouts';
    public const RESTORE = 'restore_payouts';

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
            self::MANAGE => 'Управлять выплатами',
            self::CREATE => 'Создавать выплаты',
            self::READ => 'Просматривать выплаты',
            self::UPDATE => 'Редактировать выплаты',
            self::DELETE => 'Удалять выплаты',
            self::RESTORE => 'Восстанавливать удаленные выплаты',
        ];
    }
}
