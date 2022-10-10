<?php

namespace App\Components;

use App\Common\Locator;

class Loader extends Locator
{
    public static function accounts(): Account\Facade {
        return self::get(Account\Facade::class);
    }

    public static function bonuses(): Bonus\Facade
    {
        return self::get(Bonus\Facade::class);
    }

    public static function branches(): Branch\Facade
    {
        return self::get(Branch\Facade::class);
    }

    public static function classrooms(): Classroom\Facade
    {
        return self::get(Classroom\Facade::class);
    }

    public static function contracts(): Contract\Facade
    {
        return self::get(Contract\Facade::class);
    }

    public static function courses(): Course\Facade
    {
        return self::get(Course\Facade::class);
    }

    public static function customers(): Customer\Facade
    {
        return self::get(Customer\Facade::class);
    }

    public static function genres(): Genre\Facade
    {
        return self::get(Genre\Facade::class);
    }

    public static function instructors(): Instructor\Facade
    {
        return self::get(Instructor\Facade::class);
    }

    public static function intents(): Intent\Facade
    {
        return self::get(Intent\Facade::class);
    }

    public static function lessons(): Lesson\Facade
    {
        return self::get(Lesson\Facade::class);
    }

    public static function logRecords(): LogRecord\Facade
    {
        return self::get(LogRecord\Facade::class);
    }

    public static function payments(): Payment\Facade
    {
        return self::get(Payment\Facade::class);
    }

    public static function people(): Person\Facade
    {
        return self::get(Person\Facade::class);
    }

    public static function schedules(): Schedule\Facade
    {
        return self::get(Schedule\Facade::class);
    }

    public static function students(): Student\Facade
    {
        return self::get(Student\Facade::class);
    }

    public static function subscriptions(): Subscription\Facade
    {
        return self::get(Subscription\Facade::class);
    }

    public static function tariffs(): Tariff\Facade
    {
        return self::get(Tariff\Facade::class);
    }

    public static function transactions(): Transaction\Facade
    {
        return self::get(Transaction\Facade::class);
    }

    public static function users(): User\Facade
    {
        return self::get(User\Facade::class);
    }

    public static function verificationCodes(): VerificationCode\Facade
    {
        return self::get(VerificationCode\Facade::class);
    }

    public static function visits(): Visit\Facade
    {
        return self::get(Visit\Facade::class);
    }
}