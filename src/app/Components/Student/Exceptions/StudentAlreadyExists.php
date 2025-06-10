<?php

namespace App\Components\Student\Exceptions;

use App\Components\Loader;
use App\Components\Student\Formatter;
use App\Exceptions\AlreadyExistsException;

class StudentAlreadyExists extends AlreadyExistsException
{
    protected ?\App\Models\Student $student;

    public function __construct(?\App\Models\Student $student)
    {
        $this->student = $student->load('person');
        $formatted = Loader::students()->format($student, Formatter::class);
        parent::__construct($formatted);
    }

    /**
     * @return \App\Models\Student|null
     */
    public function getStudent(): ?\App\Models\Student
    {
        return $this->student;
    }
}