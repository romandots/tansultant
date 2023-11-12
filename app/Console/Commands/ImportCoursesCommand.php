<?php

namespace App\Console\Commands;

use App\Services\Import\ImportCoursesService;
use App\Services\Import\ImportService;

class ImportCoursesCommand extends ImportCommand
{
    protected ImportCoursesService $service;

    protected $signature = 'import:courses';
    protected $description = 'Import courses from old database';

    protected function getService(): ImportService
    {
        if (!isset($this->service)) {
            $this->service = new ImportCoursesService($this);
        }
        return $this->service;
    }
}