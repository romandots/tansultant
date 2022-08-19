<?php

namespace App\Components\Instructor\Exceptions;

use App\Components\Instructor\Formatter;
use App\Components\Loader;
use App\Exceptions\AlreadyExistsException;

class InstructorAlreadyExists extends AlreadyExistsException
{
    protected ?\App\Models\Instructor $instructor;

    public function __construct(?\App\Models\Instructor $instructor)
    {
        $this->instructor = $instructor;
        $formatted = Loader::instructors()->format($instructor, Formatter::class);
        parent::__construct($formatted);
    }

    /**
     * @return \App\Models\Instructor|null
     */
    public function getInstructor(): ?\App\Models\Instructor
    {
        return $this->instructor;
    }
}