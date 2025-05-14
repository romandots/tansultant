<?php

namespace App\Services\Import\Pipes\Tariff;

use App\Components\Tariff\Dto;
use App\Models\Enum\TariffStatus;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\ImportContext;
use Closure;

class MapTariffEntity implements PipeInterface
{

    public const PROLONGATION_DISCOUNT = 10;

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $lessonsCount = (int)round($ctx->old->default_periods / 2);

        $ctx->dto = new Dto($ctx->adminUser);
        $ctx->dto->name = $ctx->old->ticket_type_name;
        $ctx->dto->price = (float)$ctx->old->default_price;
        $ctx->dto->prolongation_price = $ctx->old->default_price - (
            $ctx->old->default_price * self::PROLONGATION_DISCOUNT / 100
        );
        $ctx->dto->courses_limit = $ctx->old->default_multiclass ? $lessonsCount : 1;
        $ctx->dto->visits_limit = $lessonsCount;
        $ctx->dto->days_limit = $ctx->old->default_period;
        $ctx->dto->holds_limit = $ctx->old->default_frosts;
        $ctx->dto->status = TariffStatus::ACTIVE;

        return $next($ctx);
    }
}