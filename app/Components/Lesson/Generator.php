<?php

namespace App\Components\Lesson;

use App\Common\Traits\WithLogger;
use App\Events\Schedule\ScheduleUpdatedEvent;
use App\Models\Lesson;
use App\Models\Schedule;
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


    public function updateLessonsStatuses(): int
    {
        return \clock()->event('Updating lessons status')->run(function () {

            $ongoing = $this->service->getRepository()->updateOngoingLessonsStatus();
            $this->debug("{$ongoing->count()} lessons set to ONGOING status");

            $passed = $this->service->getRepository()->updatePassedLessonsStatus();
            $this->debug("{$passed->count()} lessons set to PASSED status");

            $lessons = $ongoing->merge($passed);
            $this->dispatchEvents($lessons);

            return $lessons->count();

        });
    }

    /**
     * @param Collection<Schedule> $schedules
     * @param Carbon $date
     * @return array
     */
    protected function generateLessonsBySchedules(Collection $schedules, Carbon $date): array
    {
        return \clock()->event('Generating lessons by schedules')->run(function () use ($schedules, $date) {
            $lessons = [];
            $lessonsIds = [];
            foreach ($schedules as $schedule) {
                if ($this->service->checkIfScheduleLessonExist($schedule, $date)) {
                    $this->debug("Lesson for schedule #{$schedule->id} has been created earlier");
                    continue;
                }
                $this->debug("Creating lesson for #{$schedule->id}...");
                $lesson = $this->service->createFromScheduleOnDate($schedule, $date);
                $lessonsIds[] = $lesson->id;
                $lessons[] = $lesson;
            }

            $count = count($lessonsIds);
            if ($count > 0) {
                $this->debug("{$count} lessons generated", $lessonsIds);
            }

            $this->updateLessonsStatuses();

            return $lessons;
        });
    }

    public function generateCourseLessonsOnDate(Carbon $date, string $courseId): void
    {
        \clock()->event('Generating course lessons on date')->run(function () use ($date, $courseId) {

            $this->debug("Generating lessons for course #{$courseId} on date {$date->format('Y-m-d')}");
            $schedules = $this->schedules->getSchedulesForCourseOnDate($courseId, $date);
            $lessons = $this->generateLessonsBySchedules($schedules, $date);
            $this->dispatchEvents($lessons);

        });
    }

    public function generateLessonsOnDate(Carbon $date): void
    {
        \clock()->event('Generating lessons on date')->run(function () use ($date) {
            $this->debug("Generating lessons on date {$date->format('Y-m-d')}");
            $schedules = $this->schedules->getSchedulesOnDate($date);
            $lessons = $this->generateLessonsBySchedules($schedules, $date);
            $this->dispatchEvents($lessons);
        });
    }

    /**
     * Dispatch ScheduleUpdatedEvent for each date and branch_id
     *
     * @param iterable<Lesson> $lessons
     * @return void
     */
    protected function dispatchEvents(iterable $lessons): void
    {
        $channels = [];
        foreach ($lessons as $lesson) {
            $key = $lesson->branch_id . $lesson->starts_at->format('Y-m-d');

            if (array_key_exists($key, $channels)) {
                continue;
            }

            $channels[$key] = [
                'branch_id' => $lesson->branch_id,
                'date' => $lesson->starts_at,
            ];
        }

        foreach ($channels as $channel) {
            $this->debug("Dispatching ScheduleUpdatedEvent for branch #{$channel['branch_id']} on {$channel['date']}");
            ScheduleUpdatedEvent::dispatch($channel['date'], $channel['branch_id']);
        }
    }
}