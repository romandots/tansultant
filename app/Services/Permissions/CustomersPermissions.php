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
class CustomersPermissions
{
    public const MANAGE_CUSTOMERS = 'manage_customers';
    public const CREATE_CUSTOMERS = 'create_customers';
    public const READ_CUSTOMERS = 'read_customers';
    public const UPDATE_CUSTOMERS = 'update_customers';
    public const DELETE_CUSTOMERS = 'delete_customers';
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
            self::MANAGE_CUSTOMERS => 'Управлять покупателями',
            self::CREATE_CUSTOMERS => 'Создавать покупателей',
            self::READ_CUSTOMERS => 'Просматривать покупателей',
            self::UPDATE_CUSTOMERS => 'Обновлять покупателей',
            self::DELETE_CUSTOMERS => 'Удалять покупателей',
            self::SIGN_CONTRACTS => 'Подписывать договор',
            self::TERMINATE_CONTRACTS => 'Прекращать договор',
        ];
    }
}
