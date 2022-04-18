<?php
/**
 * File: LessonHasVisitsException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Components\Lesson\Exceptions;

/**
 * Class LessonHasVisitsException
 * @package App\Services\Lesson\Exceptions
 */
class LessonHasVisitsException extends Exception
{
    /**
     * LessonHasVisitsException constructor.
     */
    public function __construct()
    {
        parent::__construct('lesson_has_visits');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }
}
