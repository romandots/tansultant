<?php

namespace App\Components\Lesson\Exceptions;

use App\Exceptions\InvalidStatusException;

class InvalidLessonStatusException extends InvalidStatusException
{
    public function __construct(object $currentStatus, array $allowedStatuses = [], string $message = 'invalid_lesson_status')
    {
        parent::__construct($currentStatus->value, $allowedStatuses, $message);
    }

}