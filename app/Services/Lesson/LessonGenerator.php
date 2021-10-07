<?php

namespace App\Services\Lesson;

use App\Models\Schedule;
use App\Repository\ScheduleRepository;
use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class LessonGenerator extends BaseService
{
    protected LessonService $service;
    protected ScheduleRepository $scheduleRepository;

    public function __construct(LessonService $service, ScheduleRepository $repository)
    {
        $this->service = $service;
        $this->scheduleRepository = $repository;
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

    protected function getLoggerPrefix(): string
    {
        return __CLASS__;
    }

    public function generateCourseLessonsOnDate(Carbon $date, string $courseId): void
    {
        $this->debug("Generating lessons for course #{$courseId} on date {$date->format('Y-m-d')}");
        $schedules = $this->scheduleRepository->getSchedulesForCourseOnDate($courseId, $date);
        $this->generateLessonsBySchedules($schedules, $date);
    }

    public function generateLessonsOnDate(Carbon $date): void
    {
        $this->debug("Generating lessons on date {$date->format('Y-m-d')}");
        $schedules = $this->scheduleRepository->getSchedulesOnDate($date);
        $this->generateLessonsBySchedules($schedules, $date);
    }
}