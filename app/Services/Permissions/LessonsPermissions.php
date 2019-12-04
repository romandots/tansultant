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
    public const MANAGE = 'manage_lessons';
    public const CREATE = 'create_lessons';
    public const READ = 'read_lessons';
    public const UPDATE = 'update_lessons';
    public const DELETE = 'delete_lessons';
    public const CANCEL = 'cancel_lessons';
    public const CLOSE = 'close_lessons';
    public const OPEN = 'open_lessons';
    public const BOOK = 'book_lessons';

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
            self::MANAGE => 'Управлять уроками',
            self::CREATE => 'Создавать уроки',
            self::READ => 'Просматривать уроки',
            self::UPDATE => 'Обновлять уроки',
            self::DELETE => 'Удалять уроки',
            self::CANCEL => 'Отменять уроки',
            self::CLOSE => 'Закрывать уроки',
            self::OPEN => 'Отменять закрытие урока',
            self::BOOK => 'Отменять отмену урока',
        ];
    }
}
