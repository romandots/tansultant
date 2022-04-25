<?php
/**
 * File: admin_api.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

/**
 * АПИ для админ-панели и админского приложения
 *
 * Общие методы для всех сущностей:
 * - методы GET ../suggest -- саджест с поиском и возвратом индексированного списка имен
 * - методы GET ../search -- полноценный поиск записей с постраничным возвратом
 * - методы GET ../ -- возвращают все записи (использовать с осторожностью)
 * - методы GET ../ -- возвращают запись
 * - методы POST ../ -- создают новую запись
 * - методы PUT ../{id} -- изменяю существующую запись
 * - методы DELETE ../{id} -- удаляют запись
 * - методы POST ../{id}/restore -- восстанавливают запись (доступно не для всех сущностей)
 */

declare(strict_types=1);

use App\Common\Route;
use App\Http\Controllers\ManagerApi;
use App\Services\Permissions\AccountsPermission;
use App\Services\Permissions\BranchesPermission;
use App\Services\Permissions\ClassroomsPermission;
use App\Services\Permissions\CoursesPermission;
use App\Services\Permissions\CustomersPermission;
use App\Services\Permissions\InstructorsPermission;
use App\Services\Permissions\IntentsPermission;
use App\Services\Permissions\LessonsPermission;
use App\Services\Permissions\PersonsPermission;
use App\Services\Permissions\SchedulesPermission;
use App\Services\Permissions\StudentsPermission;
use App\Services\Permissions\UsersPermission;
use App\Services\Permissions\VisitsPermission;

// ACCOUNTS
Route::namedGroup('accounts',ManagerApi\AccountController::class, static function () {
    Route::namedRoute('search', 'get', '/', [AccountsPermission::MANAGE, AccountsPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [AccountsPermission::MANAGE, AccountsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [AccountsPermission::MANAGE, AccountsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [AccountsPermission::MANAGE, AccountsPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [AccountsPermission::MANAGE, AccountsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [AccountsPermission::MANAGE, AccountsPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id}/restore', [AccountsPermission::MANAGE, AccountsPermission::RESTORE]);
});

// BRANCHES
Route::namedGroup('branches',ManagerApi\BranchController::class, static function () {
    Route::namedRoute('search', 'get', '/', [BranchesPermission::MANAGE, BranchesPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [BranchesPermission::MANAGE, BranchesPermission::READ]);
    Route::namedRoute('store', 'post', '/', [BranchesPermission::MANAGE, BranchesPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [BranchesPermission::MANAGE, BranchesPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [BranchesPermission::MANAGE, BranchesPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [BranchesPermission::MANAGE, BranchesPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id}/restore', [BranchesPermission::MANAGE, BranchesPermission::RESTORE]);
});

// CLASSROOMS
Route::namedGroup('classrooms',ManagerApi\ClassroomController::class, static function () {
    Route::namedRoute('search', 'get', '/', [ClassroomsPermission::MANAGE, ClassroomsPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [ClassroomsPermission::MANAGE, ClassroomsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [ClassroomsPermission::MANAGE, ClassroomsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [ClassroomsPermission::MANAGE, ClassroomsPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [ClassroomsPermission::MANAGE, ClassroomsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [ClassroomsPermission::MANAGE, ClassroomsPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id}/restore', [ClassroomsPermission::MANAGE, ClassroomsPermission::RESTORE]);
});

// COURSES
Route::namedGroup('courses',ManagerApi\CourseController::class, static function () {
    Route::namedRoute('search', 'get', '/', [CoursesPermission::MANAGE, CoursesPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [CoursesPermission::MANAGE, CoursesPermission::READ]);
    Route::namedRoute('store', 'post', '/', [CoursesPermission::MANAGE, CoursesPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [CoursesPermission::MANAGE, CoursesPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [CoursesPermission::MANAGE, CoursesPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [CoursesPermission::MANAGE, CoursesPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id}/restore', [CoursesPermission::MANAGE, CoursesPermission::RESTORE]);
    Route::namedRoute('disable', 'post', '{id}/disable', [CoursesPermission::MANAGE, CoursesPermission::DISABLE]);
    Route::namedRoute('enable', 'post', '{id}/enable', [CoursesPermission::MANAGE, CoursesPermission::ENABLE]);
});

// CUSTOMERS
Route::namedGroup('customers',ManagerApi\CustomerController::class, static function () {
    Route::namedRoute('search', 'get', '/', [CustomersPermission::MANAGE, CustomersPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [CustomersPermission::MANAGE, CustomersPermission::READ]);
    Route::namedRoute('store', 'post', '/', [CustomersPermission::MANAGE, CustomersPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [CustomersPermission::MANAGE, CustomersPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [CustomersPermission::MANAGE, CustomersPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [CustomersPermission::MANAGE, CustomersPermission::DELETE]);
    Route::namedRoute('sign', 'post', '{id}/contract', [CustomersPermission::MANAGE, CustomersPermission::SIGN_CONTRACTS]);
    Route::namedRoute('terminate', 'delete', '{id}/contract', [CustomersPermission::MANAGE, CustomersPermission::TERMINATE_CONTRACTS]);
});

// INSTRUCTORS
Route::namedGroup('instructors',ManagerApi\InstructorController::class, static function () {
    Route::namedRoute('search', 'get', '/', [InstructorsPermission::MANAGE, InstructorsPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [InstructorsPermission::MANAGE, InstructorsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [InstructorsPermission::MANAGE, InstructorsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [InstructorsPermission::MANAGE, InstructorsPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [InstructorsPermission::MANAGE, InstructorsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [InstructorsPermission::MANAGE, InstructorsPermission::DELETE]);
});

// INTENTS
Route::namedGroup('intents',ManagerApi\IntentController::class, static function () {
    Route::namedRoute('search', 'get', '/', [IntentsPermission::MANAGE, IntentsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [IntentsPermission::MANAGE, IntentsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [IntentsPermission::MANAGE, IntentsPermission::READ]);
    Route::namedRoute('destroy', 'delete', '{id}', [IntentsPermission::MANAGE, IntentsPermission::DELETE]);
});

// LESSONS
Route::namedGroup('lessons',ManagerApi\LessonController::class, static function () {
    Route::namedRoute('search', 'get', '/', [LessonsPermission::MANAGE, LessonsPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [LessonsPermission::MANAGE, LessonsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [LessonsPermission::MANAGE, LessonsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [LessonsPermission::MANAGE, LessonsPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [LessonsPermission::MANAGE, LessonsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [LessonsPermission::MANAGE, LessonsPermission::DELETE]);
    Route::namedRoute('cancel', 'post', '{id}\cancel', [LessonsPermission::MANAGE, LessonsPermission::CANCEL]);
    Route::namedRoute('book', 'post', '{id}\book', [LessonsPermission::MANAGE, LessonsPermission::BOOK]);
    Route::namedRoute('close', 'post', '{id}\close', [LessonsPermission::MANAGE, LessonsPermission::CLOSE]);
    Route::namedRoute('open', 'post', '{id}\open', [LessonsPermission::MANAGE, LessonsPermission::OPEN]);
});

// PEOPLE
Route::namedGroup('people',ManagerApi\PersonController::class, static function () {
    Route::namedRoute('index', 'get', '/', [PersonsPermission::MANAGE, PersonsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [PersonsPermission::MANAGE, PersonsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [PersonsPermission::MANAGE, PersonsPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [PersonsPermission::MANAGE, PersonsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [PersonsPermission::MANAGE, PersonsPermission::DELETE]);
});

// SCHEDULES
Route::namedGroup('schedules',ManagerApi\ScheduleController::class, static function () {
    Route::namedRoute('search', 'get', '/', [SchedulesPermission::MANAGE, SchedulesPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [SchedulesPermission::MANAGE, SchedulesPermission::READ]);
    Route::namedRoute('store', 'post', '/', [SchedulesPermission::MANAGE, SchedulesPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [SchedulesPermission::MANAGE, SchedulesPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [SchedulesPermission::MANAGE, SchedulesPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [SchedulesPermission::MANAGE, SchedulesPermission::DELETE]);
});

// STUDENTS
Route::namedGroup('students',ManagerApi\StudentController::class, static function () {
    Route::namedRoute('index', 'get', '/', [StudentsPermission::MANAGE, StudentsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [StudentsPermission::MANAGE, StudentsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [StudentsPermission::MANAGE, StudentsPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [StudentsPermission::MANAGE, StudentsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [StudentsPermission::MANAGE, StudentsPermission::DELETE]);
});

// USERS
Route::namedGroup('users',ManagerApi\UserController::class, static function () {
    Route::namedRoute('index', 'get', '/', [UsersPermission::MANAGE, UsersPermission::READ]);
    Route::namedRoute('store', 'post', '/', [UsersPermission::MANAGE, UsersPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [UsersPermission::MANAGE, UsersPermission::READ]);
    Route::namedRoute('update', 'put', '{id}', [UsersPermission::MANAGE, UsersPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [UsersPermission::MANAGE, UsersPermission::DELETE]);
});

// VISITS
Route::namedGroup('visits',ManagerApi\VisitController::class, static function () {
    Route::namedRoute('search', 'get', '/', [VisitsPermission::MANAGE, VisitsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [VisitsPermission::MANAGE, VisitsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [VisitsPermission::MANAGE, VisitsPermission::READ]);
    Route::namedRoute('destroy', 'delete', '{id}', [VisitsPermission::MANAGE, VisitsPermission::DELETE]);
});
