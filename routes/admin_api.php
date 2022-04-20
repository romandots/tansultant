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
use App\Services\Permissions\BranchesPermissions;
use App\Services\Permissions\ClassroomsPermissions;
use App\Services\Permissions\CoursesPermissions;
use App\Services\Permissions\CustomersPermissions;
use App\Services\Permissions\InstructorsPermissions;
use App\Services\Permissions\IntentsPermissions;
use App\Services\Permissions\LessonsPermissions;
use App\Services\Permissions\PersonsPermissions;
use App\Services\Permissions\SchedulesPermissions;
use App\Services\Permissions\StudentsPermissions;
use App\Services\Permissions\UsersPermissions;
use App\Services\Permissions\VisitsPermissions;

// USERS
Route::namedGroup('users',ManagerApi\UserController::class, static function () {
        Route::namedRoute('index', 'get', '/', [UsersPermissions::MANAGE, UsersPermissions::READ]);
        Route::namedRoute('store', 'post', '/', [UsersPermissions::MANAGE, UsersPermissions::CREATE]);
        Route::namedRoute('show', 'get', '{id}', [UsersPermissions::MANAGE, UsersPermissions::READ]);
        Route::namedRoute('update', 'put', '{id}', [UsersPermissions::MANAGE, UsersPermissions::UPDATE]);
        Route::namedRoute('destroy', 'delete', '{id}', [UsersPermissions::MANAGE, UsersPermissions::DELETE]);
});

// PEOPLE
Route::namedGroup('people',ManagerApi\PersonController::class, static function () {
    Route::namedRoute('index', 'get', '/', [PersonsPermissions::MANAGE, PersonsPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [PersonsPermissions::MANAGE, PersonsPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [PersonsPermissions::MANAGE, PersonsPermissions::READ]);
    Route::namedRoute('update', 'put', '{id}', [PersonsPermissions::MANAGE, PersonsPermissions::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [PersonsPermissions::MANAGE, PersonsPermissions::DELETE]);
});

// STUDENTS
Route::namedGroup('students',ManagerApi\StudentController::class, static function () {
    Route::namedRoute('index', 'get', '/', [StudentsPermissions::MANAGE, StudentsPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [StudentsPermissions::MANAGE, StudentsPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [StudentsPermissions::MANAGE, StudentsPermissions::READ]);
    Route::namedRoute('update', 'put', '{id}', [StudentsPermissions::MANAGE, StudentsPermissions::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [StudentsPermissions::MANAGE, StudentsPermissions::DELETE]);
});


// INSTRUCTORS
Route::namedGroup('instructors',ManagerApi\InstructorController::class, static function () {
    Route::namedRoute('search', 'get', '/', [InstructorsPermissions::MANAGE, InstructorsPermissions::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [InstructorsPermissions::MANAGE, InstructorsPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [InstructorsPermissions::MANAGE, InstructorsPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [InstructorsPermissions::MANAGE, InstructorsPermissions::READ]);
    Route::namedRoute('update', 'put', '{id}', [InstructorsPermissions::MANAGE, InstructorsPermissions::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [InstructorsPermissions::MANAGE, InstructorsPermissions::DELETE]);
});

// CUSTOMERS
Route::namedGroup('customers',ManagerApi\CustomerController::class, static function () {
    Route::namedRoute('search', 'get', '/', [CustomersPermissions::MANAGE, CustomersPermissions::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [CustomersPermissions::MANAGE, CustomersPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [CustomersPermissions::MANAGE, CustomersPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [CustomersPermissions::MANAGE, CustomersPermissions::READ]);
    Route::namedRoute('update', 'put', '{id}', [CustomersPermissions::MANAGE, CustomersPermissions::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [CustomersPermissions::MANAGE, CustomersPermissions::DELETE]);
    Route::namedRoute('sign', 'post', '{id}/contract', [CustomersPermissions::MANAGE, CustomersPermissions::SIGN_CONTRACTS]);
    Route::namedRoute('terminate', 'delete', '{id}/contract', [CustomersPermissions::MANAGE, CustomersPermissions::TERMINATE_CONTRACTS]);
});

// COURSES
Route::namedGroup('courses',ManagerApi\CourseController::class, static function () {
    Route::namedRoute('search', 'get', '/', [CoursesPermissions::MANAGE, CoursesPermissions::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [CoursesPermissions::MANAGE, CoursesPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [CoursesPermissions::MANAGE, CoursesPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [CoursesPermissions::MANAGE, CoursesPermissions::READ]);
    Route::namedRoute('update', 'put', '{id}', [CoursesPermissions::MANAGE, CoursesPermissions::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [CoursesPermissions::MANAGE, CoursesPermissions::DELETE]);
    Route::namedRoute('restore', 'post', '{id}/restore', [CoursesPermissions::MANAGE, CoursesPermissions::RESTORE]);
    Route::namedRoute('disable', 'post', '{id}/disable', [CoursesPermissions::MANAGE, CoursesPermissions::DISABLE]);
    Route::namedRoute('enable', 'post', '{id}/enable', [CoursesPermissions::MANAGE, CoursesPermissions::ENABLE]);
});

// SCHEDULES
Route::namedGroup('schedules',ManagerApi\ScheduleController::class, static function () {
    Route::namedRoute('search', 'get', '/', [SchedulesPermissions::MANAGE, SchedulesPermissions::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [SchedulesPermissions::MANAGE, SchedulesPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [SchedulesPermissions::MANAGE, SchedulesPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [SchedulesPermissions::MANAGE, SchedulesPermissions::READ]);
    Route::namedRoute('update', 'put', '{id}', [SchedulesPermissions::MANAGE, SchedulesPermissions::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [SchedulesPermissions::MANAGE, SchedulesPermissions::DELETE]);
});

// LESSONS
Route::namedGroup('lessons',ManagerApi\LessonController::class, static function () {
    Route::namedRoute('search', 'get', '/', [LessonsPermissions::MANAGE, LessonsPermissions::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [LessonsPermissions::MANAGE, LessonsPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [LessonsPermissions::MANAGE, LessonsPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [LessonsPermissions::MANAGE, LessonsPermissions::READ]);
    Route::namedRoute('update', 'put', '{id}', [LessonsPermissions::MANAGE, LessonsPermissions::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [LessonsPermissions::MANAGE, LessonsPermissions::DELETE]);
    Route::namedRoute('cancel', 'post', '{id}\cancel', [LessonsPermissions::MANAGE, LessonsPermissions::CANCEL]);
    Route::namedRoute('book', 'post', '{id}\book', [LessonsPermissions::MANAGE, LessonsPermissions::BOOK]);
    Route::namedRoute('close', 'post', '{id}\close', [LessonsPermissions::MANAGE, LessonsPermissions::CLOSE]);
    Route::namedRoute('open', 'post', '{id}\open', [LessonsPermissions::MANAGE, LessonsPermissions::OPEN]);
});

// VISITS
Route::namedGroup('visits',ManagerApi\VisitController::class, static function () {
    Route::namedRoute('search', 'get', '/', [VisitsPermissions::MANAGE, VisitsPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [VisitsPermissions::MANAGE, VisitsPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [VisitsPermissions::MANAGE, VisitsPermissions::READ]);
    Route::namedRoute('destroy', 'delete', '{id}', [VisitsPermissions::MANAGE, VisitsPermissions::DELETE]);
});

// INTENTS
Route::namedGroup('intents',ManagerApi\IntentController::class, static function () {
    Route::namedRoute('search', 'get', '/', [IntentsPermissions::MANAGE, IntentsPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [IntentsPermissions::MANAGE, IntentsPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [IntentsPermissions::MANAGE, IntentsPermissions::READ]);
    Route::namedRoute('destroy', 'delete', '{id}', [IntentsPermissions::MANAGE, IntentsPermissions::DELETE]);
});

// BRANCHES
Route::namedGroup('branches',ManagerApi\BranchController::class, static function () {
    Route::namedRoute('search', 'get', '/', [BranchesPermissions::MANAGE, BranchesPermissions::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [BranchesPermissions::MANAGE, BranchesPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [BranchesPermissions::MANAGE, BranchesPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [BranchesPermissions::MANAGE, BranchesPermissions::READ]);
    Route::namedRoute('update', 'put', '{id}', [BranchesPermissions::MANAGE, BranchesPermissions::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [BranchesPermissions::MANAGE, BranchesPermissions::DELETE]);
    Route::namedRoute('restore', 'post', '{id}/restore', [BranchesPermissions::MANAGE, BranchesPermissions::RESTORE]);
});

// CLASSROOMS
Route::namedGroup('classrooms',ManagerApi\ClassroomController::class, static function () {
    Route::namedRoute('search', 'get', '/', [ClassroomsPermissions::MANAGE, ClassroomsPermissions::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [ClassroomsPermissions::MANAGE, ClassroomsPermissions::READ]);
    Route::namedRoute('store', 'post', '/', [ClassroomsPermissions::MANAGE, ClassroomsPermissions::CREATE]);
    Route::namedRoute('show', 'get', '{id}', [ClassroomsPermissions::MANAGE, ClassroomsPermissions::READ]);
    Route::namedRoute('update', 'put', '{id}', [ClassroomsPermissions::MANAGE, ClassroomsPermissions::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id}', [ClassroomsPermissions::MANAGE, ClassroomsPermissions::DELETE]);
    Route::namedRoute('restore', 'post', '{id}/restore', [ClassroomsPermissions::MANAGE, ClassroomsPermissions::RESTORE]);
});
