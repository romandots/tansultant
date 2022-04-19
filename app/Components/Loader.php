<?php

namespace App\Components;

use App\Common\Locator;

class Loader extends Locator
{
    public static function accounts(): Account\Facade {
        return self::facade(Account\Facade::class);
    }

    public static function bonuses(): Bonus\ComponentFacade
    {
        return self::facade(Bonus\ComponentFacade::class);
    }

    public static function branches(): Branch\ComponentFacade
    {
        return self::facade(Branch\ComponentFacade::class);
    }

    public static function classrooms(): Classroom\ComponentFacade
    {
        return self::facade(Classroom\ComponentFacade::class);
    }

    public static function contracts(): Contract\ComponentFacade
    {
        return self::facade(Contract\ComponentFacade::class);
    }

    public static function customers(): Customer\ComponentFacade
    {
        return self::facade(Customer\ComponentFacade::class);
    }

    public static function genres(): Genre\ComponentFacade
    {
        return self::facade(Genre\ComponentFacade::class);
    }

    public static function instructors(): Instructor\ComponentFacade
    {
        return self::facade(Instructor\ComponentFacade::class);
    }

    public static function intents(): Intent\ComponentFacade
    {
        return self::facade(Intent\ComponentFacade::class);
    }

    public static function lessons(): Lesson\ComponentFacade
    {
        return self::facade(Lesson\ComponentFacade::class);
    }

    public static function payments(): Payment\ComponentFacade
    {
        return self::facade(Payment\ComponentFacade::class);
    }

    public static function people(): Person\ComponentFacade
    {
        return self::facade(Person\ComponentFacade::class);
    }

    public static function schedules(): Schedule\ComponentFacade
    {
        return self::facade(Schedule\ComponentFacade::class);
    }

    public static function students(): Student\ComponentFacade
    {
        return self::facade(Student\ComponentFacade::class);
    }

    public static function users(): User\ComponentFacade
    {
        return self::facade(User\ComponentFacade::class);
    }

    public static function verificationCodes(): VerificationCode\ComponentFacade
    {
        return self::facade(VerificationCode\ComponentFacade::class);
    }

    public static function visits(): Visit\ComponentFacade
    {
        return self::facade(Visit\ComponentFacade::class);
    }
}