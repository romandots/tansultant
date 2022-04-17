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
class ManagerRole extends Permissions
{
    public const ROLE = 'manager';
    public const PERMISSIONS = [
        SystemPermissions::ACCESS_PANEL,

        BranchesPermissions::MANAGE,
        BranchesPermissions::CREATE,
        BranchesPermissions::READ,
        BranchesPermissions::UPDATE,
        BranchesPermissions::DELETE,
        BranchesPermissions::RESTORE,

        ClassroomsPermissions::MANAGE,
        ClassroomsPermissions::CREATE,
        ClassroomsPermissions::READ,
        ClassroomsPermissions::UPDATE,
        ClassroomsPermissions::DELETE,
        ClassroomsPermissions::RESTORE,

        CoursesPermissions::MANAGE,
        CoursesPermissions::CREATE,
        CoursesPermissions::READ,
        CoursesPermissions::UPDATE,
        CoursesPermissions::DELETE,
        CoursesPermissions::RESTORE,

        CustomersPermissions::MANAGE,
        CustomersPermissions::CREATE,
        CustomersPermissions::READ,
        CustomersPermissions::UPDATE,
        CustomersPermissions::DELETE,
        CustomersPermissions::RESTORE,
        CustomersPermissions::SIGN_CONTRACTS,
        CustomersPermissions::TERMINATE_CONTRACTS,

        InstructorsPermissions::MANAGE,
        InstructorsPermissions::CREATE,
        InstructorsPermissions::READ,
        InstructorsPermissions::UPDATE,
        InstructorsPermissions::DELETE,
        InstructorsPermissions::RESTORE,

        IntentsPermissions::MANAGE,
        IntentsPermissions::CREATE,
        IntentsPermissions::READ,
        IntentsPermissions::UPDATE,
        IntentsPermissions::DELETE,
        IntentsPermissions::RESTORE,

        LessonsPermissions::MANAGE,
        LessonsPermissions::CREATE,
        LessonsPermissions::READ,
        LessonsPermissions::UPDATE,
        LessonsPermissions::DELETE,
        LessonsPermissions::RESTORE,
        LessonsPermissions::CANCEL,
        LessonsPermissions::CLOSE,
        LessonsPermissions::OPEN,
        LessonsPermissions::BOOK,

        PersonsPermissions::MANAGE,
        PersonsPermissions::CREATE,
        PersonsPermissions::READ,
        PersonsPermissions::UPDATE,
        PersonsPermissions::DELETE,
        PersonsPermissions::RESTORE,

        SchedulesPermissions::MANAGE,
        SchedulesPermissions::CREATE,
        SchedulesPermissions::READ,
        SchedulesPermissions::UPDATE,
        SchedulesPermissions::DELETE,
        SchedulesPermissions::RESTORE,

        StudentsPermissions::MANAGE,
        StudentsPermissions::CREATE,
        StudentsPermissions::READ,
        StudentsPermissions::UPDATE,
        StudentsPermissions::DELETE,
        StudentsPermissions::RESTORE,

        UsersPermissions::MANAGE,
        UsersPermissions::CREATE,
        UsersPermissions::READ,
        UsersPermissions::UPDATE,
        UsersPermissions::DELETE,
        UsersPermissions::RESTORE,

        VisitsPermissions::MANAGE,
        VisitsPermissions::CREATE,
        VisitsPermissions::READ,
        VisitsPermissions::UPDATE,
        VisitsPermissions::DELETE,
        VisitsPermissions::RESTORE,
    ];
}
