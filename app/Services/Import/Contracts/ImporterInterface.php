<?php

namespace App\Services\Import\Contracts;

use App\Services\Import\ImportContext;

interface ImporterInterface
{
    public function import(ImportContext $ctx): void;
}