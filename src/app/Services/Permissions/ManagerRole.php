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
class ManagerRole extends Permission
{
    public const ROLE = 'manager';
    public const PERMISSIONS = [
        SystemPermission::ACCESS_PANEL,

        BonusesPermission::MANAGE,
        BonusesPermission::CREATE,
        BonusesPermission::READ,
        BonusesPermission::UPDATE,
        BonusesPermission::DELETE,
        BonusesPermission::RESTORE,

        BranchesPermission::MANAGE,
        BranchesPermission::CREATE,
        BranchesPermission::READ,
        BranchesPermission::UPDATE,
        BranchesPermission::DELETE,
        BranchesPermission::RESTORE,

        ClassroomsPermission::MANAGE,
        ClassroomsPermission::CREATE,
        ClassroomsPermission::READ,
        ClassroomsPermission::UPDATE,
        ClassroomsPermission::DELETE,
        ClassroomsPermission::RESTORE,

        CoursesPermission::MANAGE,
        CoursesPermission::CREATE,
        CoursesPermission::READ,
        CoursesPermission::UPDATE,
        CoursesPermission::DELETE,
        CoursesPermission::RESTORE,

        CustomersPermission::MANAGE,
        CustomersPermission::CREATE,
        CustomersPermission::READ,
        CustomersPermission::UPDATE,
        CustomersPermission::DELETE,
        CustomersPermission::RESTORE,
        CustomersPermission::SIGN_CONTRACTS,
        CustomersPermission::TERMINATE_CONTRACTS,

        TransactionsPermission::CREATE,
        TransactionsPermission::CREATE_DEPOSIT,

        InstructorsPermission::MANAGE,
        InstructorsPermission::CREATE,
        InstructorsPermission::READ,
        InstructorsPermission::UPDATE,
        InstructorsPermission::DELETE,
        InstructorsPermission::RESTORE,

        IntentsPermission::MANAGE,
        IntentsPermission::CREATE,
        IntentsPermission::READ,
        IntentsPermission::UPDATE,
        IntentsPermission::DELETE,
        IntentsPermission::RESTORE,

        LessonsPermission::MANAGE,
        LessonsPermission::CREATE,
        LessonsPermission::READ,
        LessonsPermission::UPDATE,
        LessonsPermission::DELETE,
        LessonsPermission::RESTORE,
        LessonsPermission::CANCEL,
        LessonsPermission::CLOSE,
        LessonsPermission::OPEN,
        LessonsPermission::BOOK,

        PersonsPermission::MANAGE,
        PersonsPermission::CREATE,
        PersonsPermission::READ,
        PersonsPermission::UPDATE,
        PersonsPermission::DELETE,
        PersonsPermission::RESTORE,

        SchedulesPermission::MANAGE,
        SchedulesPermission::CREATE,
        SchedulesPermission::READ,
        SchedulesPermission::UPDATE,
        SchedulesPermission::DELETE,
        SchedulesPermission::RESTORE,

        ShiftsPermission::CREATE,
        ShiftsPermission::READ_OWN,
        ShiftsPermission::UPDATE,

        StudentsPermission::MANAGE,
        StudentsPermission::CREATE,
        StudentsPermission::READ,
        StudentsPermission::UPDATE,
        StudentsPermission::DELETE,
        StudentsPermission::RESTORE,

        UsersPermission::MANAGE,
        UsersPermission::CREATE,
        UsersPermission::READ,
        UsersPermission::UPDATE,
        UsersPermission::DELETE,
        UsersPermission::RESTORE,

        VisitsPermission::MANAGE,
        VisitsPermission::CREATE,
        VisitsPermission::READ,
        VisitsPermission::UPDATE,
        VisitsPermission::DELETE,
        VisitsPermission::RESTORE,
    ];
}
