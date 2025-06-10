<?php

namespace App\Models\Enum;

use App\Common\Contracts\ClassBackedEnum;

enum UserType: string implements ClassBackedEnum
{
    case USER = 'user';
    case INSTRUCTOR = 'instructor';
    case CUSTOMER = 'customer';
    case STUDENT = 'student';

    public function getClass(): string
    {
        return match($this) {
            self::USER => \App\Models\User::class,
            self::INSTRUCTOR => \App\Models\Instructor::class,
            self::CUSTOMER => \App\Models\Customer::class,
            self::STUDENT => \App\Models\Student::class,
        };
    }
}