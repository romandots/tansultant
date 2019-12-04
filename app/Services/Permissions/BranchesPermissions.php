<?php
/**
 * File: BranchesPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-08-1
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class BranchesPermissions
 * @package App\Services\Permissions
 */
class BranchesPermissions
{
    public const MANAGE = 'manage_branches';
    public const CREATE = 'create_branches';
    public const READ = 'read_branches';
    public const UPDATE = 'update_branches';
    public const DELETE = 'delete_branches';
    public const RESTORE = 'restore_branches';

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
            self::MANAGE => 'Управлять филиалами',
            self::CREATE => 'Создавать филиалы',
            self::READ => 'Просматривать филиалы',
            self::UPDATE => 'Обновлять филиалы',
            self::DELETE => 'Удалять филиалы',
            self::RESTORE => 'Восстанавливать удаленные филиалы',
        ];
    }
}
