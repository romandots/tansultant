<?php
/**
 * File: api.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-18
 * Copyright (c) 2019
 */

declare(strict_types=1);

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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('user', 'UserController@me');
Route::patch('user/password', 'UserController@updatePassword');

Route::group(['prefix' => 'users'], static function () {
    Route::post('from_person', 'UserController@createFromPerson')
        ->middleware('permission:' . UsersPermissions::MANAGE_USERS . '|' . UsersPermissions::CREATE_USERS);
    Route::post('/', 'UserController@store')
        ->middleware('permission:' . UsersPermissions::MANAGE_USERS . '|' . UsersPermissions::CREATE_USERS);
    Route::get('{id}', 'UserController@show')
        ->middleware('permission:' . UsersPermissions::MANAGE_USERS . '|' . UsersPermissions::READ_USERS);
    Route::patch('{id}', 'UserController@update')
        ->middleware('permission:' . UsersPermissions::MANAGE_USERS . '|' . UsersPermissions::UPDATE_USERS);
    Route::delete('{id}', 'UserController@destroy')
        ->middleware('permission:' . UsersPermissions::MANAGE_USERS . '|' . UsersPermissions::DELETE_USERS);
});

Route::group(['prefix' => 'people'], static function () {
    Route::post('/', 'PersonController@store')
        ->middleware('permission:' . PersonsPermissions::MANAGE_PERSONS . '|' . PersonsPermissions::CREATE_PERSONS);
    Route::get('{id}', 'PersonController@show')
        ->middleware('permission:' . PersonsPermissions::MANAGE_PERSONS . '|' . PersonsPermissions::READ_PERSONS);
    Route::patch('{id}', 'PersonController@update')
        ->middleware('permission:' . PersonsPermissions::MANAGE_PERSONS . '|' . PersonsPermissions::UPDATE_PERSONS);
    Route::delete('{id}', 'PersonController@destroy')
        ->middleware('permission:' . PersonsPermissions::MANAGE_PERSONS . '|' . PersonsPermissions::DELETE_PERSONS);
});

Route::group(['prefix' => 'students'], static function () {
    Route::post('from_person', 'StudentController@createFromPerson')
        ->middleware('permission:' . StudentsPermissions::MANAGE_STUDENTS . '|' . StudentsPermissions::CREATE_STUDENTS);
    Route::post('/', 'StudentController@store')
        ->middleware('permission:' . StudentsPermissions::MANAGE_STUDENTS . '|' . StudentsPermissions::CREATE_STUDENTS);
    Route::get('{id}', 'StudentController@show')
        ->middleware('permission:' . StudentsPermissions::MANAGE_STUDENTS . '|' . StudentsPermissions::READ_STUDENTS);
    Route::patch('{id}', 'StudentController@update')
        ->middleware('permission:' . StudentsPermissions::MANAGE_STUDENTS . '|' . StudentsPermissions::UPDATE_STUDENTS);
    Route::delete('{id}', 'StudentController@destroy')
        ->middleware('permission:' . StudentsPermissions::MANAGE_STUDENTS . '|' . StudentsPermissions::DELETE_STUDENTS);
});

Route::group(['prefix' => 'instructors'], static function () {
    Route::post('from_person', 'InstructorController@createFromPerson')
        ->middleware('permission:' . InstructorsPermissions::MANAGE_INSTRUCTORS . '|' . InstructorsPermissions::CREATE_INSTRUCTORS);
    Route::post('/', 'InstructorController@store')
        ->middleware('permission:' . InstructorsPermissions::MANAGE_INSTRUCTORS . '|' . InstructorsPermissions::CREATE_INSTRUCTORS);
    Route::get('{id}', 'InstructorController@show')
        ->middleware('permission:' . InstructorsPermissions::MANAGE_INSTRUCTORS . '|' . InstructorsPermissions::READ_INSTRUCTORS);
    Route::patch('{id}', 'InstructorController@update')
        ->middleware('permission:' . InstructorsPermissions::MANAGE_INSTRUCTORS . '|' . InstructorsPermissions::UPDATE_INSTRUCTORS);
    Route::delete('{id}', 'InstructorController@destroy')
        ->middleware('permission:' . InstructorsPermissions::MANAGE_INSTRUCTORS . '|' . InstructorsPermissions::DELETE_INSTRUCTORS);
});

Route::group(['prefix' => 'customers'], static function () {
    Route::post('from_person', 'CustomerController@createFromPerson')
        ->middleware('permission:' . CustomersPermissions::MANAGE_CUSTOMERS . '|' . CustomersPermissions::CREATE_CUSTOMERS);
    Route::post('/', 'CustomerController@store')
        ->middleware('permission:' . CustomersPermissions::MANAGE_CUSTOMERS . '|' . CustomersPermissions::CREATE_CUSTOMERS);
    Route::get('{id}', 'CustomerController@show')
        ->middleware('permission:' . CustomersPermissions::MANAGE_CUSTOMERS . '|' . CustomersPermissions::READ_CUSTOMERS);
    Route::delete('{id}', 'CustomerController@destroy')
        ->middleware('permission:' . CustomersPermissions::MANAGE_CUSTOMERS . '|' . CustomersPermissions::DELETE_CUSTOMERS);
    Route::get('{id}/contract', 'ContractController@show')
        ->middleware('permission:' . CustomersPermissions::MANAGE_CUSTOMERS . '|' . CustomersPermissions::READ_CUSTOMERS);
    Route::post('{id}/contract', 'ContractController@sign')
        ->middleware('permission:' . CustomersPermissions::SIGN_CONTRACTS);
    Route::delete('{id}/contract', 'ContractController@terminate')
        ->middleware('permission:' . CustomersPermissions::TERMINATE_CONTRACTS);
});

Route::group(['prefix' => 'courses'], static function () {
    Route::post('/', 'CourseController@store')
        ->middleware('permission:' . CoursesPermissions::MANAGE_COURSES . '|' . CoursesPermissions::CREATE_COURSES);
    Route::patch('{id}', 'CourseController@update')
        ->middleware('permission:' . CoursesPermissions::MANAGE_COURSES . '|' . CoursesPermissions::UPDATE_COURSES);
    Route::delete('{id}', 'CourseController@destroy')
        ->middleware('permission:' . CoursesPermissions::MANAGE_COURSES . '|' . CoursesPermissions::DELETE_COURSES);
    Route::get('{id}', 'CourseController@show')
        ->middleware('permission:' . CoursesPermissions::MANAGE_COURSES . '|' . CoursesPermissions::READ_COURSES);
});

Route::group(['prefix' => 'schedules'], static function () {
    Route::get('date', 'ScheduleController@onDate')
        ->middleware('permission:' . SchedulesPermissions::MANAGE_SCHEDULES . '|' . SchedulesPermissions::READ_SCHEDULES);
    Route::post('/', 'ScheduleController@store')
        ->middleware('permission:' . SchedulesPermissions::MANAGE_SCHEDULES . '|' . SchedulesPermissions::CREATE_SCHEDULES);
    Route::patch('{id}', 'ScheduleController@update')
        ->middleware('permission:' . SchedulesPermissions::MANAGE_SCHEDULES . '|' . SchedulesPermissions::UPDATE_SCHEDULES);
    Route::delete('{id}', 'ScheduleController@destroy')
        ->middleware('permission:' . SchedulesPermissions::MANAGE_SCHEDULES . '|' . SchedulesPermissions::DELETE_SCHEDULES);
    Route::get('{id}', 'ScheduleController@show')
        ->middleware('permission:' . SchedulesPermissions::MANAGE_SCHEDULES . '|' . SchedulesPermissions::READ_SCHEDULES);
});

Route::group(['prefix' => 'lessons'], static function () {
    Route::get('date', 'LessonController@onDate')
        ->middleware('permission:' . LessonsPermissions::MANAGE_LESSONS . '|' . LessonsPermissions::READ_LESSONS);
    Route::post('/', 'LessonController@store')
        ->middleware('permission:' . LessonsPermissions::MANAGE_LESSONS . '|' . LessonsPermissions::CREATE_LESSONS);
    Route::patch('{id}', 'LessonController@update')
        ->middleware('permission:' . LessonsPermissions::MANAGE_LESSONS . '|' . LessonsPermissions::UPDATE_LESSONS);
    Route::delete('{id}', 'LessonController@destroy')
        ->middleware('permission:' . LessonsPermissions::MANAGE_LESSONS . '|' . LessonsPermissions::DELETE_LESSONS);
    Route::get('{id}', 'LessonController@show')
        ->middleware('permission:' . LessonsPermissions::MANAGE_LESSONS . '|' . LessonsPermissions::READ_LESSONS);

    Route::post('{id}/cancel', 'LessonController@cancel')
        ->middleware('permission:' . LessonsPermissions::MANAGE_LESSONS . '|' .
            LessonsPermissions::CANCEL_LESSONS);
    Route::post('{id}/book', 'LessonController@book')
        ->middleware('permission:' . LessonsPermissions::MANAGE_LESSONS . '|' .
            LessonsPermissions::BOOK_LESSONS);
    Route::post('{id}/close', 'LessonController@close')
        ->middleware('permission:' . LessonsPermissions::MANAGE_LESSONS . '|' . LessonsPermissions::CLOSE_LESSONS);
    Route::post('{id}/open', 'LessonController@open')
        ->middleware('permission:' . LessonsPermissions::MANAGE_LESSONS . '|' . LessonsPermissions::OPEN_LESSONS);
});

Route::group(['prefix' => 'visits'], static function () {
    Route::post('/', 'VisitController@store')
        ->middleware('permission:' . VisitsPermissions::MANAGE_VISITS . '|' . VisitsPermissions::CREATE_VISITS);
    Route::patch('{id}', 'VisitController@update')
        ->middleware('permission:' . VisitsPermissions::MANAGE_VISITS . '|' . VisitsPermissions::UPDATE_VISITS);
    Route::delete('{id}', 'VisitController@destroy')
        ->middleware('permission:' . VisitsPermissions::MANAGE_VISITS . '|' . VisitsPermissions::DELETE_VISITS);
    Route::get('{id}', 'VisitController@show')
        ->middleware('permission:' . VisitsPermissions::MANAGE_VISITS . '|' . VisitsPermissions::READ_VISITS);
});

Route::group(['prefix' => 'intents'], static function () {
    Route::post('/', 'IntentController@store')
        ->middleware('permission:' . IntentsPermissions::MANAGE_INTENTS . '|' . IntentsPermissions::CREATE_INTENTS);
    Route::patch('{id}', 'IntentController@update')
        ->middleware('permission:' . IntentsPermissions::MANAGE_INTENTS . '|' . IntentsPermissions::UPDATE_INTENTS);
    Route::delete('{id}', 'IntentController@destroy')
        ->middleware('permission:' . IntentsPermissions::MANAGE_INTENTS . '|' . IntentsPermissions::DELETE_INTENTS);
    Route::get('{id}', 'IntentController@show')
        ->middleware('permission:' . IntentsPermissions::MANAGE_INTENTS . '|' . IntentsPermissions::READ_INTENTS);
});
