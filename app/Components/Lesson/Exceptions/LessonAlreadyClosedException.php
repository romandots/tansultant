<?php
/**
 * File: LessonAlreadyClosedException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Components\Lesson\Exceptions;

/**
 * Class LessonAlreadyClosedException
 * @package App\Services\Lesson\Exceptions
 */
class LessonAlreadyClosedException extends Exception
{
    /**
     * LessonAlreadyClosedException constructor.
     */
    public function __construct()
    {
        parent::__construct('lesson_already_closed');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }
}
