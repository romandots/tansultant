<?php
/**
 * File: manager_api.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

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
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'users'], static function () {
    Route::post('from_person', 'UserController@createFromPerson')
        ->middleware('permission:' . UsersPermissions::MANAGE . '|' . UsersPermissions::CREATE);
    Route::post('/', 'UserController@store')
        ->middleware('permission:' . UsersPermissions::MANAGE . '|' . UsersPermissions::CREATE);
    Route::get('{id}', 'UserController@show')
        ->middleware('permission:' . UsersPermissions::MANAGE . '|' . UsersPermissions::READ);
    Route::put('{id}', 'UserController@update')
        ->middleware('permission:' . UsersPermissions::MANAGE . '|' . UsersPermissions::UPDATE);
    Route::delete('{id}', 'UserController@destroy')
        ->middleware('permission:' . UsersPermissions::MANAGE . '|' . UsersPermissions::DELETE);
});

Route::group(['prefix' => 'people'], static function () {
    Route::post('/', 'PersonController@store')
        ->middleware('permission:' . PersonsPermissions::MANAGE . '|' . PersonsPermissions::CREATE);
    Route::get('{id}', 'PersonController@show')
        ->middleware('permission:' . PersonsPermissions::MANAGE . '|' . PersonsPermissions::READ);
    Route::put('{id}', 'PersonController@update')
        ->middleware('permission:' . PersonsPermissions::MANAGE . '|' . PersonsPermissions::UPDATE);
    Route::delete('{id}', 'PersonController@destroy')
        ->middleware('permission:' . PersonsPermissions::MANAGE . '|' . PersonsPermissions::DELETE);
});

Route::group(['prefix' => 'students'], static function () {
    Route::post('from_person', 'StudentController@createFromPerson')
        ->middleware('permission:' . StudentsPermissions::MANAGE . '|' . StudentsPermissions::CREATE);
    Route::post('/', 'StudentController@store')
        ->middleware('permission:' . StudentsPermissions::MANAGE . '|' . StudentsPermissions::CREATE);
    Route::get('{id}', 'StudentController@show')
        ->middleware('permission:' . StudentsPermissions::MANAGE . '|' . StudentsPermissions::READ);
    Route::put('{id}', 'StudentController@update')
        ->middleware('permission:' . StudentsPermissions::MANAGE . '|' . StudentsPermissions::UPDATE);
    Route::delete('{id}', 'StudentController@destroy')
        ->middleware('permission:' . StudentsPermissions::MANAGE . '|' . StudentsPermissions::DELETE);
});

Route::group(['prefix' => 'instructors'], static function () {
    Route::post('from_person', 'InstructorController@createFromPerson')
        ->middleware('permission:' . InstructorsPermissions::MANAGE . '|' . InstructorsPermissions::CREATE);
    Route::post('/', 'InstructorController@store')
        ->middleware('permission:' . InstructorsPermissions::MANAGE . '|' . InstructorsPermissions::CREATE);
    Route::get('{id}', 'InstructorController@show')
        ->middleware('permission:' . InstructorsPermissions::MANAGE . '|' . InstructorsPermissions::READ);
    Route::put('{id}', 'InstructorController@update')
        ->middleware('permission:' . InstructorsPermissions::MANAGE . '|' . InstructorsPermissions::UPDATE);
    Route::delete('{id}', 'InstructorController@destroy')
        ->middleware('permission:' . InstructorsPermissions::MANAGE . '|' . InstructorsPermissions::DELETE);
});

Route::group(['prefix' => 'customers'], static function () {
    Route::post('from_person', 'CustomerController@createFromPerson')
        ->middleware('permission:' . CustomersPermissions::MANAGE . '|' . CustomersPermissions::CREATE);
    Route::post('/', 'CustomerController@store')
        ->middleware('permission:' . CustomersPermissions::MANAGE . '|' . CustomersPermissions::CREATE);
    Route::get('{id}', 'CustomerController@show')
        ->middleware('permission:' . CustomersPermissions::MANAGE . '|' . CustomersPermissions::READ);
    Route::delete('{id}', 'CustomerController@destroy')
        ->middleware('permission:' . CustomersPermissions::MANAGE . '|' . CustomersPermissions::DELETE);
    Route::get('{id}/contract', 'ContractController@show')
        ->middleware('permission:' . CustomersPermissions::MANAGE . '|' . CustomersPermissions::READ);
    Route::post('{id}/contract', 'ContractController@sign')
        ->middleware('permission:' . CustomersPermissions::SIGN_CONTRACTS);
    Route::delete('{id}/contract', 'ContractController@terminate')
        ->middleware('permission:' . CustomersPermissions::TERMINATE_CONTRACTS);
});

Route::group(['prefix' => 'courses'], static function () {
    Route::get('/', 'CourseController@index')
        ->middleware('permission:' . CoursesPermissions::MANAGE . '|' . CoursesPermissions::READ);
    Route::post('/', 'CourseController@store')
        ->middleware('permission:' . CoursesPermissions::MANAGE . '|' . CoursesPermissions::CREATE);
    Route::put('{id}', 'CourseController@update')
        ->middleware('permission:' . CoursesPermissions::MANAGE . '|' . CoursesPermissions::UPDATE);
    Route::delete('{id}', 'CourseController@destroy')
        ->middleware('permission:' . CoursesPermissions::MANAGE . '|' . CoursesPermissions::DELETE);
    Route::get('{id}', 'CourseController@show')
        ->middleware('permission:' . CoursesPermissions::MANAGE . '|' . CoursesPermissions::READ);
});

Route::group(['prefix' => 'schedules'], static function () {
    Route::get('date', 'ScheduleController@onDate')
        ->middleware('permission:' . SchedulesPermissions::MANAGE . '|' . SchedulesPermissions::READ);
    Route::get('/', 'ScheduleController@index')
        ->middleware('permission:' . SchedulesPermissions::MANAGE . '|' . SchedulesPermissions::READ);
    Route::post('/', 'ScheduleController@store')
        ->middleware('permission:' . SchedulesPermissions::MANAGE . '|' . SchedulesPermissions::CREATE);
    Route::put('{id}', 'ScheduleController@update')
        ->middleware('permission:' . SchedulesPermissions::MANAGE . '|' . SchedulesPermissions::UPDATE);
    Route::delete('{id}', 'ScheduleController@destroy')
        ->middleware('permission:' . SchedulesPermissions::MANAGE . '|' . SchedulesPermissions::DELETE);
    Route::get('{id}', 'ScheduleController@show')
        ->middleware('permission:' . SchedulesPermissions::MANAGE . '|' . SchedulesPermissions::READ);
});

Route::group(['prefix' => 'lessons'], static function () {
    Route::get('date', 'LessonController@onDate')
        ->middleware('permission:' .
            LessonsPermissions::MANAGE . '|' . LessonsPermissions::READ);
    Route::post('/', 'LessonController@store')
        ->middleware('permission:' .
            LessonsPermissions::MANAGE . '|' . LessonsPermissions::CREATE);
    Route::put('{id}', 'LessonController@update')
        ->middleware('permission:' .
            LessonsPermissions::MANAGE . '|' . LessonsPermissions::UPDATE);
    Route::delete('{id}', 'LessonController@destroy')
        ->middleware('permission:' .
            LessonsPermissions::MANAGE . '|' . LessonsPermissions::DELETE);
    Route::get('{id}', 'LessonController@show')
        ->middleware('permission:' .
            LessonsPermissions::MANAGE . '|' . LessonsPermissions::READ);

    Route::post('{id}/cancel', 'LessonController@cancel')
        ->middleware('permission:' .
            LessonsPermissions::MANAGE . '|' . LessonsPermissions::CANCEL);
    Route::post('{id}/book', 'LessonController@book')
        ->middleware('permission:' .
            LessonsPermissions::MANAGE . '|' . LessonsPermissions::BOOK);
    Route::post('{id}/close', 'LessonController@close')
        ->middleware('permission:' .
            LessonsPermissions::MANAGE . '|' . LessonsPermissions::CLOSE);
    Route::post('{id}/open', 'LessonController@open')
        ->middleware('permission:' .
            LessonsPermissions::MANAGE . '|' . LessonsPermissions::OPEN);
});

Route::group(['prefix' => 'visits'], static function () {
    Route::post('/', 'VisitController@createLessonVisit')
        ->middleware('permission:' .
            VisitsPermissions::MANAGE . '|' . VisitsPermissions::CREATE);
    Route::delete('{id}', 'VisitController@destroy')
        ->middleware('permission:' .
            VisitsPermissions::MANAGE . '|' . VisitsPermissions::DELETE);
    Route::get('{id}', 'VisitController@show')
        ->middleware('permission:' .
            VisitsPermissions::MANAGE . '|' . VisitsPermissions::READ);
});

Route::group(['prefix' => 'intents'], static function () {
    Route::post('/', 'IntentController@store')
        ->middleware('permission:' .
            IntentsPermissions::MANAGE . '|' . IntentsPermissions::CREATE);
    Route::delete('{id}', 'IntentController@destroy')
        ->middleware('permission:' .
            IntentsPermissions::MANAGE . '|' . IntentsPermissions::DELETE);
    Route::get('{id}', 'IntentController@show')
        ->middleware('permission:' .
            IntentsPermissions::MANAGE . '|' . IntentsPermissions::READ);
});

Route::group(['prefix' => 'branches'], static function () {
    Route::get('/', 'BranchController@index')
        ->middleware('permission:' .
            BranchesPermissions::MANAGE . '|' . BranchesPermissions::READ);
    Route::get('{id}', 'BranchController@show')
        ->middleware('permission:' .
            BranchesPermissions::MANAGE . '|' . BranchesPermissions::READ);
    Route::post('/', 'BranchController@store')
        ->middleware('permission:' .
            BranchesPermissions::MANAGE . '|' . BranchesPermissions::CREATE);
    Route::put('{id}', 'BranchController@update')
        ->middleware('permission:' .
            BranchesPermissions::MANAGE . '|' . BranchesPermissions::UPDATE);
    Route::delete('{id}', 'BranchController@destroy')
        ->middleware('permission:' .
            BranchesPermissions::MANAGE . '|' . BranchesPermissions::DELETE);
    Route::post('{id}/recover', 'BranchController@restore')
        ->middleware('permission:' .
            BranchesPermissions::MANAGE . '|' . BranchesPermissions::DELETE);
});

Route::group(['prefix' => 'classrooms'], static function () {
    Route::get('/', 'ClassroomController@index')
        ->middleware('permission:' .
            ClassroomsPermissions::MANAGE . '|' . ClassroomsPermissions::READ);
    Route::get('{id}', 'ClassroomController@show')
        ->middleware('permission:' .
            ClassroomsPermissions::MANAGE . '|' . ClassroomsPermissions::READ);
    Route::post('/', 'ClassroomController@store')
        ->middleware('permission:' . ClassroomsPermissions::MANAGE . '|' . ClassroomsPermissions::CREATE);
    Route::put('{id}', 'ClassroomController@update')
        ->middleware('permission:' . ClassroomsPermissions::MANAGE . '|' . ClassroomsPermissions::UPDATE);
    Route::delete('{id}', 'ClassroomController@destroy')
        ->middleware('permission:' . ClassroomsPermissions::MANAGE . '|' . ClassroomsPermissions::DELETE);
    Route::post('{id}/recover', 'ClassroomController@restore')
        ->middleware('permission:' . ClassroomsPermissions::MANAGE . '|' .
            ClassroomsPermissions::DELETE);
});
