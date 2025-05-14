<?php

namespace App\Services\Import\Pipes\Subscription;

use App\Components\Loader;
use App\Components\Subscription\Dto;
use App\Models\Subscription;
use App\Models\Tariff;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Closure;

class AttachCoursesToSubscription implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        /** @var Dto $dto */
        $dto = $ctx->dto;

        try {
            /** @var Subscription $subscription */
            $subscription = $ctx->newRecord;
            /** @var Tariff $tariff */
            $tariff = Loader::tariffs()->findById($dto->tariff_id);
            $courses = $tariff->courses;
            Loader::subscriptions()->attachCourses($subscription, $courses, $ctx->adminUser);
            $ctx->debug('Прикрепили курсы к абонементу (' . $courses->count() . ' шт.): #' . $courses->implode('name', ', '));
        } catch (\Throwable $e) {
            throw new ImportException('Ошибка прикрепления курсов к абонементу: ' . $e->getMessage(), $ctx->toArray());
        }

        $dto->name = empty($dto->name) ? $tariff->name : $dto->name;

        return $next($ctx);
    }
}