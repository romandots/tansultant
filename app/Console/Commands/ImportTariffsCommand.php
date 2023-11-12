<?php

namespace App\Console\Commands;

use App\Services\Import\ImportService;
use App\Services\Import\ImportTariffsService;

class ImportTariffsCommand extends ImportCommand
{
    protected ImportTariffsService $service;

    protected $signature = 'import:tariffs';
    protected $description = 'Import tariffs from old database';

    protected function getService(): ImportService
    {
        if (!isset($this->service)) {
            $this->service = new ImportTariffsService($this);
        }
        return $this->service;
    }

}