<?php
/**
 * File: CustomersPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class CustomersPermissions
 * @package App\Services\Permissions
 */
class CustomersPermission extends Permission
{
    public const MANAGE = 'manage_customers';
    public const CREATE = 'create_customers';
    public const READ = 'read_customers';
    public const UPDATE = 'update_customers';
    public const DELETE = 'delete_customers';
    public const RESTORE = 'restore_customers';
    public const SIGN_CONTRACTS = 'sign_contracts';
    public const TERMINATE_CONTRACTS = 'terminate_contracts';

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
            self::MANAGE => 'Управлять покупателями',
            self::CREATE => 'Создавать покупателей',
            self::READ => 'Просматривать покупателей',
            self::UPDATE => 'Обновлять покупателей',
            self::DELETE => 'Удалять покупателей',
            self::RESTORE => 'Восстанавливать удаленных покупателей',
            self::SIGN_CONTRACTS => 'Подписывать договор',
            self::TERMINATE_CONTRACTS => 'Прекращать договор',
        ];
    }
}
