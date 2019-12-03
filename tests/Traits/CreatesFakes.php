<?php
/**
 * File: CreatesFakes.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-3
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\WithFaker;

/**
 * Trait CreatesFakes
 * @package Tests\Traits
 */
trait CreatesFakes
{
    use CreatesFakeUser, CreatesFakeLesson, CreatesFakeSchedule, CreatesFakeInstructor, CreatesFakeCourse,
        CreatesFakePerson, CreatesFakeIntent, CreatesFakeStudent, CreatesFakeBranch, CreatesFakeClassroom,
        CreatesFakeVisit, CreatesFakeAccount, CreatesFakePayment, WithFaker;
}
