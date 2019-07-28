<?php
/**
 * File: LessonNotPassedYetException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Lesson\Exceptions;

/**
 * Class LessonNotPassedYetException
 * @package App\Services\Lesson\Exceptions
 */
class LessonNotPassedYetException extends LessonServiceException
{
    /**
     * LessonNotPassedYetException constructor.
     */
    public function __construct()
    {
        parent::__construct('lesson_not_passed_yet');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }
}
