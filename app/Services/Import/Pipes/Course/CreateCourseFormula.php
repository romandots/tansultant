<?php

namespace App\Services\Import\Pipes\Course;

use App\Components\Formula\Dto;
use App\Components\Loader;
use App\Models\Enum\FormulaVar;
use App\Models\Formula;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Closure;

class CreateCourseFormula implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $formulaDto = new Dto();

        switch ($ctx->old->pay_type) {
            case 'tickets':
                $rate = $ctx->old->ticket_rate ?? 0;
                if ($rate < 1) {
                    throw new ImportException("Неверная ставка за абонемент: {$rate}");
                }
                $formulaDto->name = "{$rate} за каждый абонемент";
                $formulaDto->equation = sprintf("%d * %s", $rate, FormulaVar::ACTIVE_SUBSCRIPTIONS->value);
                break;
            case 'visits':
                $rate = $ctx->old->visit_rate ?? 0;
                if ($rate < 1) {
                    throw new ImportException("Неверная ставка за посещение: {$rate}");
                }
                $formulaDto->name = "{$rate} за каждое посещение";
                $formulaDto->equation = sprintf("%d * %s", $rate, FormulaVar::ALL_VISITS->value);
                break;
            case 'time':
                $rate = $ctx->old->time_rate ?? 0;
                if ($rate < 1) {
                    throw new ImportException("Неверная ставка за час: {$rate}");
                }
                $formulaDto->name = "{$rate} за каждый час";
                $formulaDto->equation = sprintf("%d * %s", $rate, FormulaVar::HOUR->value);
                break;
            case 'fixed':
                $rate = $ctx->old->lesson_rate ?? 0;
                if ($rate < 1) {
                    throw new ImportException("Неверная ставка за урок: {$rate}");
                }
                $formulaDto->name = "{$rate} за урок";
                $formulaDto->equation = $rate;
                break;
            default:
                throw new ImportException("Неизвестный тип оплаты курса: {$ctx->old->pay_type}");
        }

        try {
            /** @var Formula $formula */
            $formula = Loader::formulas()->create($formulaDto);
            $ctx->manager->increaseCounter('formula');
            $ctx->debug("Создали формулу {$formula->name} → #{$formula->id}");
        } catch (\Exception $e) {
            throw new ImportException("Ошибка сохранения формулы расчёта: {$e->getMessage()}");
        }

        return $next($ctx);
    }
}