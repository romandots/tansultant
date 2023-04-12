<?php
/**
 * File: AdminRole.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Permissions;

/**
 * Class AdminRole
 * @package App\Services\Permissions
 */
class AdminRole extends Permission
{
    public const ROLE = 'admin';
    public const PERMISSIONS = [
        SystemPermission::ACCESS_PANEL,

        AccountsPermission::MANAGE,
        AccountsPermission::CREATE,
        AccountsPermission::READ,
        AccountsPermission::UPDATE,
        AccountsPermission::DELETE,
        AccountsPermission::RESTORE,

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

        CreditsPermission::MANAGE,
        CreditsPermission::CREATE,
        CreditsPermission::READ,
        CreditsPermission::UPDATE,
        CreditsPermission::DELETE,

        CustomersPermission::MANAGE,
        CustomersPermission::CREATE,
        CustomersPermission::READ,
        CustomersPermission::UPDATE,
        CustomersPermission::DELETE,
        CustomersPermission::RESTORE,
        CustomersPermission::SIGN_CONTRACTS,
        CustomersPermission::TERMINATE_CONTRACTS,

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

        FormulasPermission::MANAGE,
        FormulasPermission::CREATE,
        FormulasPermission::READ,
        FormulasPermission::UPDATE,
        FormulasPermission::DELETE,
        FormulasPermission::RESTORE,

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

        PaymentsPermission::MANAGE,
        PaymentsPermission::CREATE,
        PaymentsPermission::READ,
        PaymentsPermission::UPDATE,
        PaymentsPermission::DELETE,
        PaymentsPermission::RESTORE,

        PayoutsPermission::MANAGE,
        PayoutsPermission::CREATE,
        PayoutsPermission::READ,
        PayoutsPermission::UPDATE,
        PayoutsPermission::CHECKOUT,
        PayoutsPermission::DELETE,
        PayoutsPermission::RESTORE,

        PersonsPermission::MANAGE,
        PersonsPermission::CREATE,
        PersonsPermission::READ,
        PersonsPermission::UPDATE,
        PersonsPermission::DELETE,
        PersonsPermission::RESTORE,

        PricesPermission::MANAGE,
        PricesPermission::CREATE,
        PricesPermission::READ,
        PricesPermission::UPDATE,
        PricesPermission::DELETE,
        PricesPermission::RESTORE,

        ShiftsPermission::MANAGE,
        ShiftsPermission::CREATE,
        ShiftsPermission::READ,
        ShiftsPermission::UPDATE,
        ShiftsPermission::DELETE,

        SchedulesPermission::MANAGE,
        SchedulesPermission::CREATE,
        SchedulesPermission::READ,
        SchedulesPermission::UPDATE,
        SchedulesPermission::DELETE,
        SchedulesPermission::RESTORE,

        StudentsPermission::MANAGE,
        StudentsPermission::CREATE,
        StudentsPermission::READ,
        StudentsPermission::UPDATE,
        StudentsPermission::DELETE,
        StudentsPermission::RESTORE,

        SubscriptionsPermission::MANAGE,
        SubscriptionsPermission::CREATE,
        SubscriptionsPermission::READ,
        SubscriptionsPermission::UPDATE,
        SubscriptionsPermission::DELETE,
        SubscriptionsPermission::RESTORE,

        TariffsPermission::MANAGE,
        TariffsPermission::CREATE,
        TariffsPermission::READ,
        TariffsPermission::UPDATE,
        TariffsPermission::DELETE,
        TariffsPermission::RESTORE,

        TransactionsPermission::MANAGE,
        TransactionsPermission::CREATE,
        TransactionsPermission::READ,
        TransactionsPermission::UPDATE,
        TransactionsPermission::DELETE,
        TransactionsPermission::RESTORE,
        TransactionsPermission::CREATE_WITHOUT_SHIFT,

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
