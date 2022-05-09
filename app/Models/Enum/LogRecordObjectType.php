<?php

namespace App\Models\Enum;

use App\Common\Contracts\ClassBackedEnum;

enum LogRecordObjectType: string implements ClassBackedEnum
{
    case COURSE = 'course';
    case USER = 'user';
    case PERSON = 'person';
    case INSTRUCTOR = 'instructor';
    case STUDENT = 'student';
    case CUSTOMER = 'customer';
    case ACCOUNT = 'account';
    case CLASSROOM = 'classroom';
    case CONTRACT = 'contract';
    case GENRE = 'genre';
    case INTENT = 'intent';
    case LESSON = 'lesson';
    case PAYMENT = 'payment';
    case SCHEDULE = 'schedule';
    case VISIT = 'visit';

    public function getClass(): string
    {
        return match($this) {
            self::COURSE => \App\Models\Course::class,
            self::USER => \App\Models\User::class,
            self::PERSON => \App\Models\Person::class,
            self::INSTRUCTOR => \App\Models\Instructor::class,
            self::STUDENT => \App\Models\Student::class,
            self::CUSTOMER => \App\Models\Customer::class,
            self::ACCOUNT => \App\Models\Account::class,
            self::CLASSROOM => \App\Models\Classroom::class,
            self::CONTRACT => \App\Models\Contract::class,
            self::GENRE => \App\Models\Genre::class,
            self::INTENT => \App\Models\Intent::class,
            self::LESSON => \App\Models\Lesson::class,
            self::PAYMENT => \App\Models\Payment::class,
            self::SCHEDULE => \App\Models\Schedule::class,
            self::VISIT => \App\Models\Visit::class,
        };
    }

    public static function getFromClass(string $className): self
    {
        return match($className) {
            \App\Models\Course::class => self::COURSE,
            \App\Models\User::class => self::USER,
            \App\Models\Person::class => self::PERSON,
            \App\Models\Instructor::class => self::INSTRUCTOR,
            \App\Models\Student::class => self::STUDENT,
            \App\Models\Customer::class => self::CUSTOMER,
            \App\Models\Account::class => self::ACCOUNT,
            \App\Models\Classroom::class => self::CLASSROOM,
            \App\Models\Contract::class => self::CONTRACT,
            \App\Models\Genre::class => self::GENRE,
            \App\Models\Intent::class => self::INTENT,
            \App\Models\Lesson::class => self::LESSON,
            \App\Models\Payment::class => self::PAYMENT,
            \App\Models\Schedule::class => self::SCHEDULE,
            \App\Models\Visit::class => self::VISIT,
        };
    }
}