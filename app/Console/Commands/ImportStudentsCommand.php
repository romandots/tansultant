<?php

namespace App\Console\Commands;

use App\Services\Import\ImportService;
use App\Services\Import\ImportStudentsService;

class ImportStudentsCommand extends ImportCommand
{
    protected ImportStudentsService $service;

    protected $signature = 'import:students';
    protected $description = 'Import students from old database';

    protected function getService(): ImportService
    {
        if (!isset($this->service)) {
            $this->service = new ImportStudentsService($this);
        }
        return $this->service;
    }

}