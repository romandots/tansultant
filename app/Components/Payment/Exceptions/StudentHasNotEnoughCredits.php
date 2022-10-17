<?php

namespace App\Components\Payment\Exceptions;

use App\Components\Loader;

class StudentHasNotEnoughCredits extends Exception
{
    public function __construct(public readonly \App\Models\Student $student, public readonly int $credits)
    {
        parent::__construct('student_has_not_enough_credits', [
            'student' => $this->student->name,
            'available_credits' => Loader::credits()->getCustomerCredits($this->student->customer),
            'required_credits' => $this->credits
        ], 409);
    }
}