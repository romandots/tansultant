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
class IntentsPermissions
{
    public const MANAGE_INTENTS = 'manage_intents';
    public const CREATE_INTENTS = 'create_intents';
    public const READ_INTENTS = 'read_intents';
    public const DELETE_INTENTS = 'delete_intents';

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
            self::MANAGE_INTENTS => 'Управлять записями на урок',
            self::CREATE_INTENTS => 'Создавать записи на урок',
            self::READ_INTENTS => 'Просматривать записи на урок',
            self::DELETE_INTENTS => 'Удалять записи на урок',
        ];
    }
}
