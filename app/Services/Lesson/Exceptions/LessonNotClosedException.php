<?php
/**
 * File: LessonNotClosedException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Lesson\Exceptions;

/**
 * Class LessonNotClosedException
 * @package App\Services\Lesson\Exceptions
 */
class LessonNotClosedException extends LessonServiceException
{
    /**
     * LessonNotClosedException constructor.
     */
    public function __construct()
    {
        parent::__construct('lesson_not_closed');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }
}
