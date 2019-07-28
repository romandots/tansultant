<?php
/**
 * File: LessonAlreadyCanceledException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Lesson\Exceptions;

/**
 * Class LessonAlreadyCanceledException
 * @package App\Services\Lesson\Exceptions
 */
class LessonAlreadyCanceledException extends LessonServiceException
{
    /**
     * LessonAlreadyCanceledException constructor.
     */
    public function __construct()
    {
        parent::__construct('lesson_already_canceled');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }
}
