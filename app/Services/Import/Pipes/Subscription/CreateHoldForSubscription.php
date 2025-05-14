<?php

namespace App\Services\Import\Pipes\Subscription;

use App\Components\Hold\Dto;
use App\Components\Loader;
use App\Models\Hold;
use App\Models\Subscription;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Closure;

class CreateHoldForSubscription implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        if ($ctx->old->status !== 'frozen' || $ctx->old->unfrozen !== null || $ctx->old->frozen === null) {
            return $next($ctx);
        }

        $dto = new Dto($ctx->adminUser);
        $dto->subscription_id = $ctx->newId;

        try {
            $dto->starts_at = Carbon::createFromFormat('Y-m-d', $ctx->old->frozen);
        } catch(InvalidFormatException) {
            throw new ImportException('Нераспознанная дата заморозки: ' . $ctx->old->frozen);
        }

        try {
            /** @var Hold $hold */
            $hold = Loader::holds()->create($dto);
            $ctx->debug('Создали заморозку → #' . $hold->id);
            $ctx->manager->increaseCounter('hold');
        } catch (\Throwable $e) {
            throw new ImportException('Ошибка создания заморозки: ' . $e->getMessage(), $ctx->toArray());
        }

        try {
            /** @var Subscription $subscription */
            $subscription = $ctx->newRecord;
            Loader::subscriptions()->hold($subscription, $hold, $ctx->adminUser);
        } catch (\Throwable $e) {
            throw new ImportException('Ошибка применения заморозки к абонементу: ' . $e->getMessage(), $ctx->toArray());
        }

        return $next($ctx);
    }
}