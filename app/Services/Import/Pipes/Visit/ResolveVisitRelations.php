<?php

namespace App\Services\Import\Pipes\Visit;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class ResolveVisitRelations implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        return $next($ctx);
    }
}