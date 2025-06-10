<?php

namespace App\Services\Import\Pipes\Tariff;

use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportSkippedException;
use App\Services\Import\ImportContext;
use Closure;

class SkipInactiveTariff implements PipeInterface
{
    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        if ($ctx->old->ticket_type_active === 0) {
            throw new ImportSkippedException(
                "Тариф {$ctx->old->ticket_type_name} неактивен"
            );
        }
        return $next($ctx);
    }
}