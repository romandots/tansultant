<?php

namespace App\Console\Commands;

use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCommand extends Command
{
    protected $signature = 'import {entity : Ключ сущности из config(\'import.map\') или "all"}';
    protected $description = 'Импорт записей из старой базы';

    public function handle(): int
    {
        $entity = $this->argument('entity');
        $map = config('import.map', []);

        if ($entity === 'all') {
            foreach (array_keys($map) as $key) {
                $this->info("=== Импорт сущности «{$key}» ===");
                $this->importEntity($key);
            }
        } else {
            if (!isset($map[$entity])) {
                $this->error("Неизвестная сущность: {$entity}");
                return 1;
            }
            $this->info("=== Импорт сущности «{$entity}» ===");
            $this->importEntity($entity);
        }

        $this->info('Импорт завершён.');
        return 0;
    }

    protected function importEntity(string $entity): void
    {
        /** @var ImportManager $importManager */
        $importManager = app(ImportManager::class);

        $mapEntry = config("import.map.{$entity}");
        $table = $mapEntry['table'];
        $chunkSize = $mapEntry['chunk'] ?? 500;

        // Берём подключение к старой БД явно, чтобы chunkById работал корректно
        $connection = DB::connection('old_database');

        $connection
            ->table($table)
            ->orderBy('id')
            ->chunkById($chunkSize, function ($rows) use ($entity, $importManager) {
                foreach ($rows as $old) {
                    $id = $old->id;
                    $this->info("Импорт {$entity} #{$id}");
                    try {
                        $importManager->ensureImported($entity, $id);
                    } catch (ImportException $e) {
                        $this->error("Ошибка импорта {$entity} #{$id}: {$e->getMessage()}");
                    }
                }
            });
    }
}
