<?php

namespace App\Console\Commands;

use App\Services\Import\CliLogger;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\Exceptions\ImportSkippedException;
use App\Services\Import\ImportManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCommand extends Command
{
    protected $signature = 'import {entity : Ключ сущности из config(\'import.map\') или "all"} {--retry : Перезапускать только записи с ошибками} {--force : Перезапустить импорт всех записей}';
    protected $description = 'Импорт записей из старой базы';
    protected ImportManager $importManager;

    public function handle(): int
    {
        // temporary switch global logger to file
        config(['logging.default' => 'single']);

        $entity = $this->argument('entity');
        $retry  = $this->option('retry');
        $force = $this->option('force');

        $offset = config('import.offset',);
        $map = config('import.map', []);
        $all = config('import.essential_entities', array_keys($map));
        $this->importManager = app(ImportManager::class);
        $this->importManager->setLogger(new CliLogger($this));

        $this->info($retry ? 'Перезапуск импорта неуспешных записей' : 'Импорт всех записей не импортированных раннее');
        $this->info("Импортируем данные начиная с {$offset}");

        if ($entity === 'all') {
            foreach ($all as $key) {
                $this->info("=== Импорт сущности «{$key}» ===");
                $this->importEntity($key, $retry, $force);
            }
        } else {
            if (!isset($map[$entity])) {
                $this->error("Неизвестная сущность: {$entity}");
                return 1;
            }
            $this->info("=== Импорт сущности «{$entity}» ===");
            $result = $this->importEntity($entity, $retry, $force);
        }

        $this->info('Импорт завершён. Добавлено записей: ' . $this->importManager->getImportTotalCount());
        $this->info($this->importManager->getImportCount());
        return $result ?? 0;
    }

    protected function importEntity(string $entity, bool $retry, bool $force): int
    {
        $lock = cache()->lock('import-' . $entity . '-lock', 30 * 60);
        if (!$lock->get()){
            $this->error("Импорт сущности «{$entity}» уже выполняется в другом процессе");
            return 1;
        }

        // Release lock upon script termination
        if (function_exists('pcntl_signal')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGINT, function () use ($lock) {
                $this->info('Скрипт завершён. Снимаем блокировку.');
                optional($lock)->release();
                exit(1);
            });
        }

        try {
            $mapEntry = config("import.map.{$entity}");
            $table = $mapEntry['table'] ?? null;
            $additionalWhereClause = $mapEntry['where'] ?? null;

            if (!$table) {
                return 1;
            }

            if ($retry) {
                $ids = \App\Models\IdMap::query()
                    ->where('entity', $entity)
                    ->whereNotNull('error')
                    ->pluck('old_id')
                    ->map(fn($i) => (string)$i)
                    ->all();

                if (empty($ids)) {
                    $this->info("Нет записей с ошибками для повторного импорта «{$entity}»");
                    return 0;
                }
            } elseif (!$force) {
                $ids = \App\Models\IdMap::query()
                    ->where('entity', $entity)
                    ->pluck('old_id')
                    ->map(fn($i) => (string)$i)
                    ->all();
            } else {
                $ids = [];
            }

            $query = DB::connection('import_source_database')
                ->table($table)
                ->orderBy('id');

            if ($additionalWhereClause) {
                $query->whereRaw($additionalWhereClause);
            }

            if ($retry) {
                // только те, что упали
                $query->whereIn('id', $ids);
            } elseif (!empty($ids)) {
                // все, что уже импортировали (успешно или с ошибкой) — пропускаем
                $query->whereNotIn('id', $ids);
            }

            $chunkSize = $mapEntry['chunk'] ?? 500;
            $query
                ->chunkById($chunkSize, function ($rows) use ($entity) {
                    foreach ($rows as $old) {
                        try {
                            $this->importManager->ensureImported($entity, $old->id, 0);
                        } catch (ImportSkippedException $e) {
                            $prefix = $this->getLogPrefix($e->getData()['level'] ?? 0, $entity, $old->id);
                            $this->info("{$prefix}: Пропускаем импорт: {$e->getMessage()}");
                            continue;
                        } catch (ImportException $e) {
                            $prefix = $this->getLogPrefix($e->getData()['level'] ?? 0, $entity, $old->id);
                            $this->error("{$prefix}: Ошибка импорта: {$e->getMessage()}");
                        }
                    }
                });

            return 0;
        } finally {
            $lock->release();
        }
    }

    protected function getLogPrefix(int $level, string $entity, string|int $oldId): string
    {
        return sprintf('%s%s#%s', str_repeat("\t", $level), $entity, (string)$oldId);
    }
}
