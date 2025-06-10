<?php

namespace App\Services\Import\Pipes\Visit;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportSkippedException;
use App\Services\Import\ImportContext;
use Closure;

class SkipInvalidVisits implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        if (!in_array($ctx->old->type, ['ticket', 'cash'])) {
            throw new ImportSkippedException("Бесплатное посещение: {$ctx->old->type}");
        }

        $offset = config('import.offset');
        if (strtotime($ctx->old->timestamp) < strtotime($offset)) {
            throw new ImportSkippedException("Посещение слишком старое: {$ctx->old->timestamp}");
        }

        return $next($ctx);
    }
}