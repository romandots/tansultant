<?php

namespace App\Console\Commands;

class LessonUpdateCommand extends LessonCommand
{
    protected $signature = 'lesson:update';
    protected $description = 'Update lessons statuses';

    public function handle(): void
    {
        $this->info('Updating lessons statuses');
        $updatedCount = $this->lessons->updateLessonsStatuses();
        $this->info("Done! {$updatedCount} lessons updated");
    }
}
