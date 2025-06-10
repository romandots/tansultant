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
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateCourseFormula implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $formulaDto = new Dto($ctx->adminUser);

        $visitRate = $ctx->old->visit_rate ?? 0;
        $ticketRate = $ctx->old->ticket_rate ?? 0;
        $timeRate = $ctx->old->time_rate ?? 0;
        $lessonRate = $ctx->old->lesson_rate ?? 0;

        if ($visitRate > 0) {
            $formulaDto->name = "{$visitRate}₽ за каждое посещение";
            $formulaDto->equation = sprintf("%d * %s", $visitRate, FormulaVar::ALL_VISITS->value);
        } elseif ($ticketRate > 0) {
            $formulaDto->name = "{$ticketRate}₽ за каждый абонемент";
            $formulaDto->equation = sprintf("%d * %s", $ticketRate, FormulaVar::ACTIVE_SUBSCRIPTIONS->value);
        } elseif ($timeRate > 0) {
            $formulaDto->name = "{$timeRate}₽ за каждый час";
            $formulaDto->equation = sprintf("%d * %s", $timeRate, FormulaVar::HOUR->value);
        } elseif ($lessonRate > 0) {
            $formulaDto->name = "{$lessonRate}₽ за урок";
            $formulaDto->equation = $lessonRate;
        } else {
            throw new ImportException("Неизвестный тип оплаты курса");
        }

        $ctx->dto->formula_id = $this->getFormulaId($formulaDto, $ctx);

        return $next($ctx);
    }

    protected function getFormulaId(Dto $formulaDto, ImportContext $ctx): string
    {
        /** @var Formula $formula */
        try {
            $formula = Loader::formulas()->findBy('equation', $formulaDto->equation);
            $ctx->debug("Формула расчёта уже существует: {$formula->name} → #{$formula->id}");
        } catch (ModelNotFoundException) {
            try {
                $formula = Loader::formulas()->create($formulaDto);
                $ctx->manager->increaseCounter('formula');
                $ctx->debug("Создали формулу {$formula->name} → #{$formula->id}");
            } catch (\Exception $e) {
                throw new ImportException("Ошибка сохранения формулы расчёта: {$e->getMessage()}");
            }
        }
        return $formula->id;
    }
}