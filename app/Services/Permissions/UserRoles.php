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
    public const ADMIN = 'admin';
    public const MANAGER = 'manager';
    public const OPERATOR = 'operator';
    public const SUPERVISOR = 'supervisor';

    public const ADMIN_PERMISSIONS = [
        SystemPermissions::ACCESS_PANEL,

        PersonsPermissions::MANAGE_PERSONS,
        PersonsPermissions::CREATE_PERSONS,
        PersonsPermissions::READ_PERSONS,
        PersonsPermissions::UPDATE_PERSONS,
        PersonsPermissions::DELETE_PERSONS,

        UsersPermissions::MANAGE_USERS,
        UsersPermissions::CREATE_USERS,
        UsersPermissions::READ_USERS,
        UsersPermissions::UPDATE_USERS,
        UsersPermissions::DELETE_USERS,

        InstructorsPermissions::MANAGE_INSTRUCTORS,
        InstructorsPermissions::CREATE_INSTRUCTORS,
        InstructorsPermissions::READ_INSTRUCTORS,
        InstructorsPermissions::UPDATE_INSTRUCTORS,
        InstructorsPermissions::DELETE_INSTRUCTORS,

        CustomersPermissions::MANAGE_CUSTOMERS,
        CustomersPermissions::CREATE_CUSTOMERS,
        CustomersPermissions::READ_CUSTOMERS,
        CustomersPermissions::UPDATE_CUSTOMERS,
        CustomersPermissions::DELETE_CUSTOMERS,
        CustomersPermissions::SIGN_CONTRACTS,
        CustomersPermissions::TERMINATE_CONTRACTS,

        StudentsPermissions::MANAGE_STUDENTS,
        StudentsPermissions::CREATE_STUDENTS,
        StudentsPermissions::READ_STUDENTS,
        StudentsPermissions::UPDATE_STUDENTS,
        StudentsPermissions::DELETE_STUDENTS,
    ];

    public const MANAGER_PERMISSIONS = [
        UsersPermissions::READ_USERS,

        PersonsPermissions::MANAGE_PERSONS,
        PersonsPermissions::CREATE_PERSONS,
        PersonsPermissions::READ_PERSONS,
        PersonsPermissions::UPDATE_PERSONS,

        InstructorsPermissions::MANAGE_INSTRUCTORS,
        InstructorsPermissions::CREATE_INSTRUCTORS,
        InstructorsPermissions::READ_INSTRUCTORS,
        InstructorsPermissions::UPDATE_INSTRUCTORS,

        CustomersPermissions::MANAGE_CUSTOMERS,
        CustomersPermissions::CREATE_CUSTOMERS,
        CustomersPermissions::READ_CUSTOMERS,
        CustomersPermissions::UPDATE_CUSTOMERS,
        CustomersPermissions::SIGN_CONTRACTS,
        CustomersPermissions::TERMINATE_CONTRACTS,

        StudentsPermissions::MANAGE_STUDENTS,
        StudentsPermissions::CREATE_STUDENTS,
        StudentsPermissions::READ_STUDENTS,
        StudentsPermissions::UPDATE_STUDENTS,
    ];

    public const OPERATOR_PERMISSIONS = [
        UsersPermissions::READ_USERS,

        PersonsPermissions::READ_PERSONS,

        InstructorsPermissions::MANAGE_INSTRUCTORS,
        InstructorsPermissions::CREATE_INSTRUCTORS,
        InstructorsPermissions::READ_INSTRUCTORS,
        InstructorsPermissions::UPDATE_INSTRUCTORS,

        CustomersPermissions::MANAGE_CUSTOMERS,
        CustomersPermissions::CREATE_CUSTOMERS,
        CustomersPermissions::READ_CUSTOMERS,
        CustomersPermissions::UPDATE_CUSTOMERS,
        CustomersPermissions::SIGN_CONTRACTS,
        CustomersPermissions::TERMINATE_CONTRACTS,

        StudentsPermissions::MANAGE_STUDENTS,
        StudentsPermissions::CREATE_STUDENTS,
        StudentsPermissions::READ_STUDENTS,
        StudentsPermissions::UPDATE_STUDENTS,
    ];

    public const SUPERVISOR_PERMISSIONS = [
        UsersPermissions::READ_USERS,

        PersonsPermissions::READ_PERSONS,

        InstructorsPermissions::MANAGE_INSTRUCTORS,
        InstructorsPermissions::CREATE_INSTRUCTORS,
        InstructorsPermissions::READ_INSTRUCTORS,
        InstructorsPermissions::UPDATE_INSTRUCTORS,

        CustomersPermissions::MANAGE_CUSTOMERS,
        CustomersPermissions::CREATE_CUSTOMERS,
        CustomersPermissions::READ_CUSTOMERS,
        CustomersPermissions::UPDATE_CUSTOMERS,
        CustomersPermissions::SIGN_CONTRACTS,
        CustomersPermissions::TERMINATE_CONTRACTS,

        StudentsPermissions::MANAGE_STUDENTS,
        StudentsPermissions::CREATE_STUDENTS,
        StudentsPermissions::READ_STUDENTS,
        StudentsPermissions::UPDATE_STUDENTS,
    ];

    public const PERMISSIONS_MAP = [
        self::ADMIN => self::ADMIN_PERMISSIONS,
        self::MANAGER => self::MANAGER_PERMISSIONS,
        self::OPERATOR => self::OPERATOR_PERMISSIONS,
        self::SUPERVISOR => self::SUPERVISOR_PERMISSIONS,
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
            self::SUPERVISOR,
        ];
    }

    /**
     * Get all built-in role descriptions
     * @return string[]
     */
    public static function getInitialDescriptions(): array
    {
        return [
            self::ADMIN => 'Администратор',
            self::MANAGER => 'Менеджер',
            self::OPERATOR => 'Оператор',
            self::SUPERVISOR => 'Супервайзер',
        ];
    }
}
