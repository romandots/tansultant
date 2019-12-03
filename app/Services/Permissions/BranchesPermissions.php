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
    public const MANAGE_BRANCHES = 'manage_branches';
    public const CREATE_BRANCHES = 'create_branches';
    public const READ_BRANCHES = 'read_branches';
    public const UPDATE_BRANCHES = 'update_branches';
    public const DELETE_BRANCHES = 'delete_branches';

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
            self::MANAGE_BRANCHES => 'Управлять филиалами',
            self::CREATE_BRANCHES => 'Создавать филиалы',
            self::READ_BRANCHES => 'Просматривать филиалы',
            self::UPDATE_BRANCHES => 'Обновлять филиалы',
            self::DELETE_BRANCHES => 'Удалять филиалы',
        ];
    }
}
