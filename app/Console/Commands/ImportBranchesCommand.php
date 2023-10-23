<?php

namespace App\Console\Commands;

use App\Services\Import\ImportBranchesService;
use App\Services\Import\ImportService;

class ImportBranchesCommand extends ImportCommand
{
    protected ImportBranchesService $service;

    protected $signature = 'import:branches';
    protected $description = 'Import branches from old database';

    protected function getService(): ImportService
    {
        if (!isset($this->service)) {
            $this->service = new ImportBranchesService($this);
        }
        return $this->service;
    }

}