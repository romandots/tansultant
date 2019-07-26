<?php
/**
 * File: LessonsPermissions.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-26
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class LessonsPermissions
 * @package App\Services\Permissions
 */
class LessonsPermissions
{
    public const MANAGE_LESSONS = 'manage_lessons';
    public const CREATE_LESSONS = 'create_lessons';
    public const READ_LESSONS = 'read_lessons';
    public const UPDATE_LESSONS = 'update_lessons';
    public const DELETE_LESSONS = 'delete_lessons';

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
            self::MANAGE_LESSONS => 'Управлять уроками',
            self::CREATE_LESSONS => 'Создавать уроки',
            self::READ_LESSONS => 'Просматривать уроки',
            self::UPDATE_LESSONS => 'Обновлять уроки',
            self::DELETE_LESSONS => 'Удалять уроки',
        ];
    }
}
