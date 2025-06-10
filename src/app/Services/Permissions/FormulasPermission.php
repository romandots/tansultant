<?php
/**
 * File: FormulasPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class FormulasPermissions
 * @package App\Services\Permissions
 */
class FormulasPermission extends Permission
{
    public const MANAGE = 'manage_formulas';
    public const CREATE = 'create_formulas';
    public const READ = 'read_formulas';
    public const UPDATE = 'update_formulas';
    public const DELETE = 'delete_formulas';
    public const RESTORE = 'restore_formulas';

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
            self::MANAGE => 'Управлять посещениями',
            self::CREATE => 'Создавать посещения',
            self::READ => 'Просматривать посещения',
            self::UPDATE => 'Редактировать посещения',
            self::DELETE => 'Удалять посещения',
            self::RESTORE => 'Восстанавливать удаленные посещения',
        ];
    }
}
