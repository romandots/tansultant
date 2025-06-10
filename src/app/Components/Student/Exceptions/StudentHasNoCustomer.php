<?php

namespace App\Components\Student\Exceptions;

use App\Models\Student;

class StudentHasNoCustomer extends Exception
{
    public function __construct(public readonly Student $student)
    {
        parent::__construct('student_has_no_customer', [
            'student_id' => $this->student
        ], 400);
    }
}