<?php
/**
 * File: InstructorStatusIncompatible.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-20
 * Copyright (c) 2020
 */

declare(strict_types=1);


namespace App\Services\Course\Exceptions;


use App\Exceptions\BaseException;
use App\Models\Instructor;

class InstructorStatusIncompatible extends BaseException
{
    public function __construct(Instructor $instructor)
    {
        parent::__construct('instructor_status_incompatible', ['instructor' => $instructor], 409);
    }
}