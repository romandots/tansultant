<?php

namespace App\Components\Payment\Exceptions;

use App\Components\Loader;

class StudentHasNotEnoughCredits extends Exception
{
    public function __construct(public readonly \App\Models\Student $student, public readonly int $credits)
    {
        $customerCredits = Loader::credits()->getCustomerCredits($this->student->customer);
        parent::__construct('student_has_not_enough_credits', [
            'student' => $this->student->name,
            'customer' => $this->student->customer->name,
            'available_credits' => $customerCredits,
            'required_credits' => $this->credits,
            'deficient_credits' => $this->credits - $customerCredits,
        ], 409);
    }
}