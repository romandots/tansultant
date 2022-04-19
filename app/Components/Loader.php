<?php

namespace App\Components;

use App\Common\Locator;

class Loader extends Locator
{
    public static function accounts(): Account\Facade {
        return self::facade(Account\Facade::class);
    }

    public static function bonuses(): Bonus\Facade
    {
        return self::facade(Bonus\Facade::class);
    }

    public static function branches(): Branch\Facade
    {
        return self::facade(Branch\Facade::class);
    }

    public static function classrooms(): Classroom\Facade
    {
        return self::facade(Classroom\Facade::class);
    }

    public static function contracts(): Contract\Facade
    {
        return self::facade(Contract\Facade::class);
    }

    public static function customers(): Customer\Facade
    {
        return self::facade(Customer\Facade::class);
    }

    public static function instructors(): Instructor\Facade
    {
        return self::facade(Instructor\Facade::class);
    }

    public static function intents(): Intent\Facade
    {
        return self::facade(Intent\Facade::class);
    }

    public static function lessons(): Lesson\Facade
    {
        return self::facade(Lesson\Facade::class);
    }

    public static function payments(): Payment\Facade
    {
        return self::facade(Payment\Facade::class);
    }

    public static function people(): Person\Facade
    {
        return self::facade(Person\Facade::class);
    }

    public static function schedules(): Schedule\Facade
    {
        return self::facade(Schedule\Facade::class);
    }

    public static function students(): Student\Facade
    {
        return self::facade(Student\Facade::class);
    }

    public static function users(): User\Facade
    {
        return self::facade(User\Facade::class);
    }

    public static function visits(): Visit\Facade
    {
        return self::facade(Visit\Facade::class);
    }
}