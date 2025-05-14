<?php

namespace App\Services\Import\Pipes\Subscription;

use App\Components\Subscription\Dto;
use App\Models\Enum\SubscriptionStatus;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Closure;

class MapSubscriptionEntity implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $lessonsCount = (int)round($ctx->old->periods / 2);

        $ctx->dto = new Dto($ctx->adminUser);
        $ctx->dto->name = $ctx->old->ticket_name ?? "";
        $ctx->dto->status = match($ctx->old->status) {
            'active' => SubscriptionStatus::ACTIVE,
            'expired' => SubscriptionStatus::EXPIRED,
            'frozen' => SubscriptionStatus::ON_HOLD,
            'future' => SubscriptionStatus::PENDING,
            default => throw new ImportException('Неизвестный статус абонемента: ' . $ctx->old->status),
        };
        $ctx->dto->days_limit = $ctx->old->period;
        $ctx->dto->holds_limit = max(0, $ctx->old->frosts - $ctx->old->frosts_used);
        $ctx->dto->courses_limit = $ctx->old->multiclass ? $lessonsCount : 1;
        $ctx->dto->visits_limit = $lessonsCount;

        return $next($ctx);
    }
}