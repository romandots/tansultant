<?php

namespace App\Services\Import\Pipes\Tariff;

use App\Components\Loader;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\Exceptions\ImportSkippedException;
use App\Services\Import\ImportContext;
use Closure;

class AttachCourses implements \App\Services\Import\Contracts\PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $classesIncluded = $ctx->old->default_classes_included;

        if (empty($classesIncluded) || $classesIncluded === 'all') {
            $classes = $ctx->manager->getOldDatabaseConnection()
                ->table('classes')
                ->whereRaw(config('import.map.course.where'))
                ->get('id')
                ->pluck('id')
                ->toArray();
            $ctx->debug('Получили все классы из старой базы: ' . implode(', ', $classes));
        } else {
            try {
                $classes = array_filter(unserialize($ctx->old->default_classes_included, ['allowed_classes' => false]));
                $ctx->debug('Получили классы из старой базы: ' . implode(', ', $classes));
            } catch (\Throwable $e) {
                $classes = [];
                $ctx->debug('Ошибка при получении классов из старой базы: ' . $e->getMessage());
            }
        }

        $classesExcluded = $ctx->old->default_classes_excluded;
        try {
            $classesExcluded = array_filter(unserialize($classesExcluded, ['allowed_classes' => false]));
            $ctx->debug('Получили исключённые классы из старой базы: ' . implode(', ', $classesExcluded));
        } catch (\Throwable $e) {
            $classesExcluded = [];
            $ctx->debug('Ошибка при получении исключённых классов из старой базы: ' . $e->getMessage());
        }

        $classes = array_diff($classes, $classesExcluded);

        if (empty($classes)) {
            $ctx->debug('Нет классов для привязки к тарифу');
            return $next($ctx);
        }

        $coursesIds = [];
        foreach ($classes as $classId) {
            try {
                $coursesIds[] = $ctx->manager->ensureImported('course', $classId, $ctx->level);
            } catch (ImportSkippedException|ImportException $exception) {
                $ctx->debug('Пропускаем импорт класса #' . $classId . ': ' . $exception->getMessage());
                continue;
            }
        }

        $courses = Loader::courses()->getMany($coursesIds);

        try {
            $ctx->debug('Привязываем курсы к тарифу (' . $courses->count() . ' шт.): ' . $courses->implode('name', ', '));
            Loader::tariffs()->getService()->attachCourses($ctx->newRecord, $courses, $ctx->adminUser);
        } catch (\Throwable $throwable) {
            throw new \App\Services\Import\Exceptions\ImportException(
                "Ошибка привязки курсов к тарифу ({$ctx->old?->ticket_type_name}): {$throwable->getMessage()}",
                $ctx->toArray()
            );
        }

        return $next($ctx);
    }
}