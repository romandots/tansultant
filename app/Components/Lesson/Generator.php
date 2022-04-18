<?php

namespace App\Components\Lesson;

use App\Models\Schedule;
use App\Services\WithLogger;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class Generator
{
    use WithLogger;

    protected Service $service;
    protected \App\Components\Schedule\Facade $schedules;

    public function __construct()
    {
        $this->service = \app(Service::class);
        $this->schedules = \app(\App\Components\Schedule\Facade::class);
    }

    protected function getLoggerPrefix(): string
    {
        return __CLASS__;
    }

    /**
     * @param Collection<Schedule> $schedules
     * @param Carbon $date
     */
    public function generateLessonsBySchedules(Collection $schedules, Carbon $date): void
    {
        $lessonsIds = [];
        foreach ($schedules as $schedule) {
            if ($this->service->checkIfScheduleLessonExist($schedule, $date)) {
                $this->debug("Lesson for schedule #{$schedule->id} has been created earlier");
                continue;
            }
            $this->debug("Creating lesson for #{$schedule->id}...");
            $lesson = $this->service->createFromScheduleOnDate($schedule, $date);
            $lessonsIds[] = $lesson->id;
        }

        $count = count($lessonsIds);
        if ($count > 0) {
            $this->debug("{$count} lessons generated", $lessonsIds);
        }
    }

    public function generateCourseLessonsOnDate(Carbon $date, string $courseId): void
    {
        $this->debug("Generating lessons for course #{$courseId} on date {$date->format('Y-m-d')}");
        $schedules = $this->schedules->getSchedulesForCourseOnDate($courseId, $date);
        $this->generateLessonsBySchedules($schedules, $date);
    }

    public function generateLessonsOnDate(Carbon $date): void
    {
        $this->debug("Generating lessons on date {$date->format('Y-m-d')}");
        $schedules = $this->schedules->getSchedulesOnDate($date);
        $this->generateLessonsBySchedules($schedules, $date);
    }
}