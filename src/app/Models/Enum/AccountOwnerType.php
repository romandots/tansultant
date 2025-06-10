<?php

namespace App\Models\Enum;

use App\Common\Contracts\ClassBackedEnum;

/**
 * @deprecated
 */
enum AccountOwnerType: string implements ClassBackedEnum
{
    case STUDENT = 'student';
    case INSTRUCTOR = 'instructor';
    case BRANCH = 'branch';

    public function getClass(): string
    {
        return match ($this) {
            self::STUDENT => \App\Models\Student::class,
            self::INSTRUCTOR => \App\Models\Instructor::class,
            self::BRANCH => \App\Models\Branch::class,
        };
    }

    public static function fromClass(string $className): self
    {
        return match ($className) {
            \App\Models\Student::class => self::STUDENT,
            \App\Models\Instructor::class => self::INSTRUCTOR,
            \App\Models\Branch::class => self::BRANCH,
        };
    }
}