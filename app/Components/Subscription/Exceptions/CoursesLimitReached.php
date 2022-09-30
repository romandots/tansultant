<?php

namespace App\Components\Subscription\Exceptions;

class CoursesLimitReached extends Exception
{
    public function __construct(int $limit)
    {
        parent::__construct('courses_limit_reached', [
            'limit' => $limit,
        ], 409);
    }
}