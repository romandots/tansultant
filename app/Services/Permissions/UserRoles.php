<?php
/**
 * File: SystemRoles.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class Roles
 * @package App\Services\Permissions
 */
class UserRoles
{
    public const ADMIN = AdminRole::ROLE;
    public const MANAGER = ManagerRole::ROLE;
    public const OPERATOR = OperatorRole::ROLE;
    public const STUDENT = StudentRole::ROLE;
    public const CUSTOMER = CustomerRole::ROLE;
    public const INSTRUCTOR = InstructorRole::ROLE;

    public const ADMIN_PERMISSIONS = AdminRole::PERMISSIONS;
    public const MANAGER_PERMISSIONS = ManagerRole::PERMISSIONS;
    public const OPERATOR_PERMISSIONS = OperatorRole::PERMISSIONS;
    public const STUDENT_PERMISSIONS = StudentRole::PERMISSIONS;
    public const CUSTOMER_PERMISSIONS = CustomerRole::PERMISSIONS;
    public const INSTRUCTOR_PERMISSIONS = InstructorRole::PERMISSIONS;

    public const PERMISSIONS_MAP = [
        self::ADMIN => self::ADMIN_PERMISSIONS,
        self::MANAGER => self::MANAGER_PERMISSIONS,
        self::OPERATOR => self::OPERATOR_PERMISSIONS,
        self::STUDENT => self::STUDENT_PERMISSIONS,
        self::CUSTOMER => self::CUSTOMER_PERMISSIONS,
        self::INSTRUCTOR => self::INSTRUCTOR_PERMISSIONS,
    ];

    /**
     * Get all built-in role names
     * @return string[]
     */
    public static function getAllNames(): array
    {
        return [
            self::ADMIN,
            self::MANAGER,
            self::OPERATOR,
            self::INSTRUCTOR,
            self::CUSTOMER,
            self::STUDENT,
        ];
    }

    /**
     * Get all built-in role descriptions
     * @return string[]
     */
    public static function getInitialDescriptions(): array
    {
        return [
            self::ADMIN => 'Администратор системы',
            self::MANAGER => 'Управляющий',
            self::OPERATOR => 'Оператор',
            self::INSTRUCTOR => 'Преподаватель',
            self::CUSTOMER => 'Покупатель',
            self::STUDENT => 'Студент',
        ];
    }
}
