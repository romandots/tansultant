<?php

namespace App\Console\Commands;

use App\Services\Import\ImportService;
use App\Services\Import\ImportSubscriptionsService;

class ImportSubscriptionsCommand extends ImportCommand
{
    protected ImportSubscriptionsService $service;

    protected $signature = 'import:subscriptions';
    protected $description = 'Import subscriptions from old database';

    protected function getService(): ImportService
    {
        if (!isset($this->service)) {
            $this->service = new ImportSubscriptionsService($this);
        }
        return $this->service;
    }

}