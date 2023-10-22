<?php

namespace App\Console\Commands;

use App\Services\Import\ImportService;

abstract class ImportCommand extends \Illuminate\Console\Command
{
    abstract protected function getService(): ImportService;

    public function handle(): void
    {
        $this->getService()->handleImportCommand();
    }
}