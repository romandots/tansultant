<?php

namespace App\Console\Commands;


use Carbon\Carbon;

class LessonGenerateCommand extends LessonCommand
{
    protected $signature = 'lesson:generate {date} {courseId?}';
    protected $description = 'Generates lessons of course on date';

    public function handle(): void
    {
        $date = Carbon::parse($this->argument('date'));
        $courseId = $this->argument('courseId');

        if ($courseId) {
            $this->info("Generating lessons for course #{$courseId} on {$date->toFormattedDateString()}");
            $beforeCount = $this->lessons->getRepository()->countCourseLessonsOnDate($courseId, $date);
            $this->lessons->generateCourseLessonsOnDate($date, $courseId);
            $afterCount = $this->lessons->getRepository()->countCourseLessonsOnDate($courseId, $date);
        } else {
            $this->info("Generating lessons on {$date->toFormattedDateString()}");
            $beforeCount = $this->lessons->getRepository()->countLessonsOnDate($date);
            $this->lessons->generateLessonsOnDate($date);
            $afterCount = $this->lessons->getRepository()->countLessonsOnDate($date);
        }

        $this->info('Done!');
        $this->info('Lessons count before generation: ' . $beforeCount);
        $this->info('Total lessons generated: ' . $afterCount - $beforeCount);

    }
}
