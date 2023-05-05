<?php

namespace App\Services\Permissions;

class TransactionsPermission extends Permission
{

    public const MANAGE = 'manage_transactions';
    public const CREATE = 'create_transactions';
    public const CREATE_DEPOSIT = 'create_deposit_transactions';
    public const READ = 'read_transactions';
    public const UPDATE = 'update_transactions';
    public const DELETE = 'delete_transactions';
    public const RESTORE = 'restore_transactions';
    public const CREATE_WITHOUT_SHIFT = 'create_without_shift_transactions';

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
            self::MANAGE => 'Управлять транзакциями',
            self::CREATE => 'Создавать транзакции',
            self::CREATE_DEPOSIT => 'Пополнять баланс клиентов',
            self::READ => 'Просматривать транзакции',
            self::UPDATE => 'Обновлять транзакции',
            self::DELETE => 'Удалять транзакции',
            self::RESTORE => 'Восстанавливать удаленные транзакции',
            self::CREATE_WITHOUT_SHIFT => 'Создавать транзакции без смены',
        ];
    }
}