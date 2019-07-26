<?php
/**
 * File: InstructorPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class InstructorsPermissions
 * @package App\Services\Permissions
 */
class InstructorsPermissions
{
    public const MANAGE_INSTRUCTORS = 'manage_instructors';
    public const CREATE_INSTRUCTORS = 'create_instructors';
    public const READ_INSTRUCTORS = 'read_instructors';
    public const UPDATE_INSTRUCTORS = 'update_instructors';
    public const DELETE_INSTRUCTORS = 'delete_instructors';

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
            self::MANAGE_INSTRUCTORS => 'Управлять инструкторами',
            self::CREATE_INSTRUCTORS => 'Создавать инструкторов',
            self::READ_INSTRUCTORS => 'Просматривать инструкторов',
            self::UPDATE_INSTRUCTORS => 'Обновлять инструкторов',
            self::DELETE_INSTRUCTORS => 'Удалять инструкторов',
        ];
    }
}
