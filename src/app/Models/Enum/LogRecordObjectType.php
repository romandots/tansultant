<?php

namespace App\Models\Enum;

use App\Common\Contracts\ClassBackedEnum;

enum LogRecordObjectType: string implements ClassBackedEnum
{
    case ACCOUNT = 'account';
    case BRANCH = 'branch';
    case CLASSROOM = 'classroom';
    case CONTRACT = 'contract';
    case COURSE = 'course';
    case CUSTOMER = 'customer';
    case CREDIT = 'credit';
    case FORMULA = 'formula';
    case GENRE = 'genre';
    case HOLD = 'hold';
    case INSTRUCTOR = 'instructor';
    case INTENT = 'intent';
    case LESSON = 'lesson';
    case PAYMENT = 'payment';
    case PERSON = 'person';
    case PRICE = 'price';
    case SCHEDULE = 'schedule';
    case STUDENT = 'student';
    case SUBSCRIPTION = 'subscription';
    case TARIFF = 'tariff';
    case TRANSACTION = 'transaction';
    case USER = 'user';
    case VISIT = 'visit';

    public function getClass(): string
    {
        return match($this) {
            self::ACCOUNT => \App\Models\Account::class,
            self::CLASSROOM => \App\Models\Classroom::class,
            self::BRANCH => \App\Models\Branch::class,
            self::CONTRACT => \App\Models\Contract::class,
            self::COURSE => \App\Models\Course::class,
            self::CUSTOMER => \App\Models\Customer::class,
            self::CREDIT => \App\Models\Credit::class,
            self::FORMULA => \App\Models\Formula::class,
            self::GENRE => \App\Models\Genre::class,
            self::HOLD => \App\Models\Hold::class,
            self::INSTRUCTOR => \App\Models\Instructor::class,
            self::INTENT => \App\Models\Intent::class,
            self::LESSON => \App\Models\Lesson::class,
            self::PAYMENT => \App\Models\Payment::class,
            self::PERSON => \App\Models\Person::class,
            self::PRICE => \App\Models\Price::class,
            self::SCHEDULE => \App\Models\Schedule::class,
            self::STUDENT => \App\Models\Student::class,
            self::SUBSCRIPTION => \App\Models\Subscription::class,
            self::TARIFF => \App\Models\Tariff::class,
            self::TRANSACTION => \App\Models\Transaction::class,
            self::USER => \App\Models\User::class,
            self::VISIT => \App\Models\Visit::class,
        };
    }

    public static function getFromClass(string $className): self
    {
        return match($className) {
            \App\Models\Account::class => self::ACCOUNT,
            \App\Models\Branch::class => self::BRANCH,
            \App\Models\Classroom::class => self::CLASSROOM,
            \App\Models\Contract::class => self::CONTRACT,
            \App\Models\Course::class => self::COURSE,
            \App\Models\Customer::class => self::CUSTOMER,
            \App\Models\Credit::class => self::CREDIT,
            \App\Models\Genre::class => self::GENRE,
            \App\Models\Hold::class => self::HOLD,
            \App\Models\Instructor::class => self::INSTRUCTOR,
            \App\Models\Intent::class => self::INTENT,
            \App\Models\Lesson::class => self::LESSON,
            \App\Models\Payment::class => self::PAYMENT,
            \App\Models\Person::class => self::PERSON,
            \App\Models\Price::class => self::PRICE,
            \App\Models\Schedule::class => self::SCHEDULE,
            \App\Models\Student::class => self::STUDENT,
            \App\Models\Subscription::class => self::SUBSCRIPTION,
            \App\Models\Tariff::class => self::TARIFF,
            \App\Models\Transaction::class => self::TRANSACTION,
            \App\Models\User::class => self::USER,
            \App\Models\Visit::class => self::VISIT,
        };
    }
}