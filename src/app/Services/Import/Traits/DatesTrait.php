<?php

namespace App\Services\Import\Traits;

trait DatesTrait
{
    protected function getLessonStartTimestamp(object $lesson): int
    {
        return strtotime($lesson->date . ' ' . $lesson->time);
    }

    protected function getLessonEndTimestamp(object $lesson): int
    {
        return $this->getLessonStartTimestamp($lesson) + ($lesson->periods * 30 * 60);
    }

    protected function getCarbonObjectFromTimestamp(int $timestamp, string $errorMessage): \Carbon\Carbon
    {
        try {
            return \Carbon\Carbon::createFromTimestamp($timestamp);
        }catch (\Carbon\Exceptions\InvalidArgumentException $e) {
            throw new \App\Services\Import\Exceptions\ImportException($errorMessage);
        }
    }

    protected function getCarbonObjectFromFormattedDate(string $date, string $format, string $errorMessage): \Carbon\Carbon
    {
        try {
            return \Carbon\Carbon::createFromFormat($format, $date);
        }catch (\Carbon\Exceptions\InvalidArgumentException $e) {
            throw new \App\Services\Import\Exceptions\ImportException($errorMessage);
        }
    }
}