<?php

namespace App\Services\Import\Contracts;

use App\Services\Import\ImportContext;
use Closure;

interface PipeInterface
{
    /**
     * @param ImportContext $ctx
     * @param Closure(ImportContext):ImportContext $next
     * @return ImportContext
     */
    public function handle(ImportContext $ctx, Closure $next): ImportContext;
}