<?php
/**
 * File: LessonNotCanceledYetException.inc
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Lesson\Exceptions;

/**
 * Class LessonNotCanceledYetException
 * @package App\Services\Lesson\Exceptions
 */
class LessonNotCanceledYetException extends LessonServiceException
{
    /**
     * LessonNotCanceledYetException constructor.
     */
    public function __construct()
    {
        parent::__construct('lesson_not_canceled_yet');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }
}
