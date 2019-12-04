<?php
/**
 * File: ManagerRole.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class ManagerRole
 * @package App\Services\Permissions
 */
class ManagerRole
{
    public const ROLE = 'manager';
    public const PERMISSIONS = [
        SystemPermissions::ACCESS_PANEL,

        BranchesPermissions::MANAGE,
        BranchesPermissions::CREATE,
        BranchesPermissions::READ,
        BranchesPermissions::UPDATE,
        BranchesPermissions::DELETE,

        ClassroomsPermissions::MANAGE,
        ClassroomsPermissions::CREATE,
        ClassroomsPermissions::READ,
        ClassroomsPermissions::UPDATE,
        ClassroomsPermissions::DELETE,

        CoursesPermissions::MANAGE,
        CoursesPermissions::CREATE,
        CoursesPermissions::READ,
        CoursesPermissions::UPDATE,
        CoursesPermissions::DELETE,

        CustomersPermissions::MANAGE,
        CustomersPermissions::CREATE,
        CustomersPermissions::READ,
        CustomersPermissions::UPDATE,
        CustomersPermissions::DELETE,
        CustomersPermissions::SIGN_CONTRACTS,
        CustomersPermissions::TERMINATE_CONTRACTS,

        InstructorsPermissions::MANAGE,
        InstructorsPermissions::CREATE,
        InstructorsPermissions::READ,
        InstructorsPermissions::UPDATE,
        InstructorsPermissions::DELETE,

        IntentsPermissions::MANAGE,
        IntentsPermissions::CREATE,
        IntentsPermissions::READ,
        IntentsPermissions::UPDATE,
        IntentsPermissions::DELETE,

        LessonsPermissions::MANAGE,
        LessonsPermissions::CREATE,
        LessonsPermissions::READ,
        LessonsPermissions::UPDATE,
        LessonsPermissions::DELETE,
        LessonsPermissions::CANCEL,
        LessonsPermissions::CLOSE,
        LessonsPermissions::OPEN,
        LessonsPermissions::BOOK,

        PersonsPermissions::MANAGE,
        PersonsPermissions::CREATE,
        PersonsPermissions::READ,
        PersonsPermissions::UPDATE,
        PersonsPermissions::DELETE,

        SchedulesPermissions::MANAGE,
        SchedulesPermissions::CREATE,
        SchedulesPermissions::READ,
        SchedulesPermissions::UPDATE,
        SchedulesPermissions::DELETE,

        StudentsPermissions::MANAGE,
        StudentsPermissions::CREATE,
        StudentsPermissions::READ,
        StudentsPermissions::UPDATE,
        StudentsPermissions::DELETE,

        UsersPermissions::MANAGE,
        UsersPermissions::CREATE,
        UsersPermissions::READ,
        UsersPermissions::UPDATE,
        UsersPermissions::DELETE,

        VisitsPermissions::MANAGE,
        VisitsPermissions::CREATE,
        VisitsPermissions::READ,
        VisitsPermissions::UPDATE,
        VisitsPermissions::DELETE,
    ];
}
