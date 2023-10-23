<?php

namespace App\Console\Commands;

use App\Services\Import\ImportClassroomsService;
use App\Services\Import\ImportService;

class ImportClassroomsCommand extends ImportCommand
{
    protected ImportClassroomsService $service;

    protected $signature = 'import:classrooms';
    protected $description = 'Import classrooms from old database';

    protected function getService(): ImportService
    {
        if (!isset($this->service)) {
            $this->service = new ImportClassroomsService($this);
        }
        return $this->service;
    }

}