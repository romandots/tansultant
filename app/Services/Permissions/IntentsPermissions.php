<?php
/**
 * File: IntentsPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class IntentsPermissions
 * @package App\Services\Permissions
 */
class IntentsPermissions extends Permissions
{
    public const MANAGE = 'manage_intents';
    public const CREATE = 'create_intents';
    public const READ = 'read_intents';
    public const UPDATE = 'update_intents';
    public const DELETE = 'delete_intents';
    public const RESTORE = 'restore_intents';

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
            self::MANAGE => 'Управлять записями на урок',
            self::CREATE => 'Создавать записи на урок',
            self::READ => 'Просматривать записи на урок',
            self::UPDATE => 'Редактировать записи на урок',
            self::DELETE => 'Удалять записи на урок',
            self::RESTORE => 'Восстанавливать удаленные записи на урок',
        ];
    }
}
