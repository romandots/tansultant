<?php
/**
 * File: LessonNotCompletelyPaidException.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-27
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Components\Lesson\Exceptions;

/**
 * Class LessonNotCompletelyPaidException
 * @package App\Services\Lesson\Exceptions
 */
class LessonNotCompletelyPaidException extends Exception
{

    /**
     * LessonNotCompletelyPaidException constructor.
     */
    public function __construct()
    {
        parent::__construct('lesson_not_completely_paid');
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 409;
    }
}
