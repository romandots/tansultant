<?php

namespace App\Models\Enum;

enum InstructorStatus: string
{
    case HIRED = 'hired';
    case FREELANCE = 'freelance';
    case FIRED = 'fired';
}