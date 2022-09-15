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
 * - методы PUT ../{id:uuid} -- изменяю существующую запись
 * - методы DELETE ../{id:uuid} -- удаляют запись
 * - методы POST ../{id:uuid}/restore -- восстанавливают запись (доступно не для всех сущностей)
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
use App\Services\Permissions\SubscriptionsPermission;
use App\Services\Permissions\UsersPermission;
use App\Services\Permissions\VisitsPermission;

Route::get('/history/{type}/{id:uuid}', '\App\Http\Controllers\ManagerApi\HistoryController@index')
    ->name('history');

// ACCOUNTS
Route::namedGroup('accounts',ManagerApi\AccountController::class, static function () {
    Route::namedRoute('search', 'get', '/', [AccountsPermission::MANAGE, AccountsPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [AccountsPermission::MANAGE, AccountsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [AccountsPermission::MANAGE, AccountsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [AccountsPermission::MANAGE, AccountsPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [AccountsPermission::MANAGE, AccountsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [AccountsPermission::MANAGE, AccountsPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id:uuid}/restore', [AccountsPermission::MANAGE, AccountsPermission::RESTORE]);
});

// BRANCHES
Route::namedGroup('branches',ManagerApi\BranchController::class, static function () {
    Route::namedRoute('search', 'get', '/', [BranchesPermission::MANAGE, BranchesPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [BranchesPermission::MANAGE, BranchesPermission::READ]);
    Route::namedRoute('store', 'post', '/', [BranchesPermission::MANAGE, BranchesPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [BranchesPermission::MANAGE, BranchesPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [BranchesPermission::MANAGE, BranchesPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [BranchesPermission::MANAGE, BranchesPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id:uuid}/restore', [BranchesPermission::MANAGE, BranchesPermission::RESTORE]);
});

// CLASSROOMS
Route::namedGroup('classrooms',ManagerApi\ClassroomController::class, static function () {
    Route::namedRoute('search', 'get', '/', [ClassroomsPermission::MANAGE, ClassroomsPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [ClassroomsPermission::MANAGE, ClassroomsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [ClassroomsPermission::MANAGE, ClassroomsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [ClassroomsPermission::MANAGE, ClassroomsPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [ClassroomsPermission::MANAGE, ClassroomsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [ClassroomsPermission::MANAGE, ClassroomsPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id:uuid}/restore', [ClassroomsPermission::MANAGE, ClassroomsPermission::RESTORE]);
});

// COURSES
Route::namedGroup('courses',ManagerApi\CourseController::class, static function () {
    Route::namedRoute('search', 'get', '/', [CoursesPermission::MANAGE, CoursesPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [CoursesPermission::MANAGE, CoursesPermission::READ]);
    Route::namedRoute('store', 'post', '/', [CoursesPermission::MANAGE, CoursesPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [CoursesPermission::MANAGE, CoursesPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [CoursesPermission::MANAGE, CoursesPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [CoursesPermission::MANAGE, CoursesPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id:uuid}/restore', [CoursesPermission::MANAGE, CoursesPermission::RESTORE]);
    Route::namedRoute('disable', 'post', '{id:uuid}/disable', [CoursesPermission::MANAGE, CoursesPermission::DISABLE]);
    Route::namedRoute('enable', 'post', '{id:uuid}/enable', [CoursesPermission::MANAGE, CoursesPermission::ENABLE]);
});

// CUSTOMERS
Route::namedGroup('customers',ManagerApi\CustomerController::class, static function () {
    Route::namedRoute('search', 'get', '/', [CustomersPermission::MANAGE, CustomersPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [CustomersPermission::MANAGE, CustomersPermission::READ]);
    Route::namedRoute('store', 'post', '/', [CustomersPermission::MANAGE, CustomersPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [CustomersPermission::MANAGE, CustomersPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [CustomersPermission::MANAGE, CustomersPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [CustomersPermission::MANAGE, CustomersPermission::DELETE]);
    Route::namedRoute('sign', 'post', '{id:uuid}/contract', [CustomersPermission::MANAGE, CustomersPermission::SIGN_CONTRACTS]);
    Route::namedRoute('terminate', 'delete', '{id:uuid}/contract', [CustomersPermission::MANAGE, CustomersPermission::TERMINATE_CONTRACTS]);
});

// INSTRUCTORS
Route::namedGroup('instructors',ManagerApi\InstructorController::class, static function () {
    Route::namedRoute('search', 'get', '/', [InstructorsPermission::MANAGE, InstructorsPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [InstructorsPermission::MANAGE, InstructorsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [InstructorsPermission::MANAGE, InstructorsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [InstructorsPermission::MANAGE, InstructorsPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [InstructorsPermission::MANAGE, InstructorsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [InstructorsPermission::MANAGE, InstructorsPermission::DELETE]);
});

// INTENTS
Route::namedGroup('intents',ManagerApi\IntentController::class, static function () {
    Route::namedRoute('search', 'get', '/', [IntentsPermission::MANAGE, IntentsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [IntentsPermission::MANAGE, IntentsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [IntentsPermission::MANAGE, IntentsPermission::READ]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [IntentsPermission::MANAGE, IntentsPermission::DELETE]);
});

// LESSONS
Route::namedGroup('lessons',ManagerApi\LessonController::class, static function () {
    Route::namedRoute('search', 'get', '/', [LessonsPermission::MANAGE, LessonsPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [LessonsPermission::MANAGE, LessonsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [LessonsPermission::MANAGE, LessonsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [LessonsPermission::MANAGE, LessonsPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [LessonsPermission::MANAGE, LessonsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [LessonsPermission::MANAGE, LessonsPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id:uuid}/restore', [LessonsPermission::MANAGE, LessonsPermission::RESTORE]);
    Route::namedRoute('cancel', 'post', '{id:uuid}/cancel', [LessonsPermission::MANAGE, LessonsPermission::CANCEL]);
    Route::namedRoute('book', 'post', '{id:uuid}/book', [LessonsPermission::MANAGE, LessonsPermission::BOOK]);
    Route::namedRoute('close', 'post', '{id:uuid}/close', [LessonsPermission::MANAGE, LessonsPermission::CLOSE]);
    Route::namedRoute('open', 'post', '{id:uuid}/open', [LessonsPermission::MANAGE, LessonsPermission::OPEN]);
    Route::namedRoute('checkout', 'post', '{id:uuid}/checkout', [LessonsPermission::MANAGE, LessonsPermission::OPEN]);
});

// PEOPLE
Route::namedGroup('people',ManagerApi\PersonController::class, static function () {
    Route::namedRoute('search', 'get', '/', [PersonsPermission::MANAGE, PersonsPermission::READ]);
    Route::namedRoute('suggest', 'get', '/suggest', [PersonsPermission::MANAGE, PersonsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [PersonsPermission::MANAGE, PersonsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [PersonsPermission::MANAGE, PersonsPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [PersonsPermission::MANAGE, PersonsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [PersonsPermission::MANAGE, PersonsPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id:uuid}/restore', [PersonsPermission::MANAGE, PersonsPermission::RESTORE]);
});

// PRICES
Route::namedGroup('prices',ManagerApi\PriceController::class, static function () {
    Route::namedRoute('search', 'get', '/', [CoursesPermission::MANAGE, CoursesPermission::READ]);
    Route::namedRoute('store', 'post', '/', [CoursesPermission::MANAGE, CoursesPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [CoursesPermission::MANAGE, CoursesPermission::READ]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [CoursesPermission::MANAGE, CoursesPermission::DELETE]);
});

// SCHEDULES
Route::namedGroup('schedules',ManagerApi\ScheduleController::class, static function () {
    Route::namedRoute('search', 'get', '/', [SchedulesPermission::MANAGE, SchedulesPermission::READ]);
    Route::namedRoute('store', 'post', '/', [SchedulesPermission::MANAGE, SchedulesPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [SchedulesPermission::MANAGE, SchedulesPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [SchedulesPermission::MANAGE, SchedulesPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [SchedulesPermission::MANAGE, SchedulesPermission::DELETE]);
});

// STUDENTS
Route::namedGroup('students',ManagerApi\StudentController::class, static function () {
    Route::namedRoute('suggest', 'get', '/suggest', [StudentsPermission::MANAGE, StudentsPermission::READ]);
    Route::namedRoute('search', 'get', '/', [StudentsPermission::MANAGE, StudentsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [StudentsPermission::MANAGE, StudentsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [StudentsPermission::MANAGE, StudentsPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [StudentsPermission::MANAGE, StudentsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [StudentsPermission::MANAGE, StudentsPermission::DELETE]);
});

// SUBSCRIPTIONS
Route::namedGroup('subscriptions',ManagerApi\SubscriptionController::class, static function () {
    Route::namedRoute('search', 'get', '/', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id:uuid}/restore', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::RESTORE]);
});

// TARIFFS
Route::namedGroup('tariffs',ManagerApi\TariffController::class, static function () {
    Route::namedRoute('suggest', 'get', '/suggest', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::READ]);
    Route::namedRoute('search', 'get', '/', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::CREATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::DELETE]);
    Route::namedRoute('restore', 'post', '{id:uuid}/restore', [SubscriptionsPermission::MANAGE, SubscriptionsPermission::RESTORE]);
});

// USERS
Route::namedGroup('users',ManagerApi\UserController::class, static function () {
    Route::namedRoute('index', 'get', '/', [UsersPermission::MANAGE, UsersPermission::READ]);
    Route::namedRoute('store', 'post', '/', [UsersPermission::MANAGE, UsersPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [UsersPermission::MANAGE, UsersPermission::READ]);
    Route::namedRoute('update', 'put', '{id:uuid}', [UsersPermission::MANAGE, UsersPermission::UPDATE]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [UsersPermission::MANAGE, UsersPermission::DELETE]);
});

// VISITS
Route::namedGroup('visits',ManagerApi\VisitController::class, static function () {
    Route::namedRoute('search', 'get', '/', [VisitsPermission::MANAGE, VisitsPermission::READ]);
    Route::namedRoute('store', 'post', '/', [VisitsPermission::MANAGE, VisitsPermission::CREATE]);
    Route::namedRoute('show', 'get', '{id:uuid}', [VisitsPermission::MANAGE, VisitsPermission::READ]);
    Route::namedRoute('destroy', 'delete', '{id:uuid}', [VisitsPermission::MANAGE, VisitsPermission::DELETE]);
});
