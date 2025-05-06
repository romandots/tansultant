<?php

namespace App\Services\Import;

use App\Common\BaseComponentService;
use App\Models\IdMap;
use App\Services\Import\Contracts\ImporterInterface;
use App\Services\Import\Exceptions\ImportException;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class ImportManager
{

    /**
     * Маппинг старых таблиц, новых моделей и импортёров
     * @var array<string, array{table?: string, model?: class-string<\Illuminate\Database\Eloquent\Model>, importer?: class-string}>
     */
    protected readonly array $map;

    /**
     * Защита от циклов
     * @var array<string, array<int,bool>>
     */
    protected array $inProgress = [];

    /**
     * Рантайм кэш
     * @var array $resolved
     */
    protected array $resolved = [];

    /**
     * Счетчик импортированных моделей
     * @var array<string, int>
     */
    protected array $importCount = [];

    public function __construct(
        protected readonly \Illuminate\Database\Connection $oldDatabase,
        protected LoggerInterface $logger,
    ) {
        $this->map = config('import.map');
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string $entity
     * @param int|string $oldId
     * @return string
     * @throws ImportException
     */
    public function ensureImported(string $entity, int|string $oldId): string
    {
        // 1) если уже в кеше — сразу отдать
        if (isset($this->resolved[$entity][$oldId])) {
            $this->logger->debug("{$entity}#{$oldId} уже импортирован - берем из кэша");
            return $this->resolved[$entity][$oldId];
        }

        // 2) если уже в БД — закэшировать и вернуть
        $existingUuid = IdMap::query()
            ->where('entity', $entity)
            ->where('old_id', (string)$oldId)
            ->value('new_id');
        if ($existingUuid) {
            $this->logger->debug("{$entity}#{$oldId} уже импортирован - берем из БД");
            return $this->resolved[$entity][$oldId] = $existingUuid;
        }

        // 3) защита от циклов
        if (!empty($this->inProgress[$entity][$oldId])) {
            throw new ImportException("Циклическая зависимость при импорте {$entity}#{$oldId}", [
                'in_progress' => $this->inProgress,
            ]);
        }
        $this->inProgress[$entity][$oldId] = true;

        // 4) достать старую модель
        $table = $this->mapKeyToOldDatabaseTable($entity);
        $old = $this->oldDatabase
            ->table($table)
            ->where('id', $oldId)
            ->first();

        if (!$old) {
            unset($this->inProgress[$entity][$oldId]);
            throw new ImportException(
                "Старая запись {$entity}#{$oldId} не найдена а таблице {$table}"
            );
        }

        // 5) Готовим контекст и запускаем импорт в транзакции
        $ctx = new ImportContext($entity, $old, $this, $this->logger);

        DB::transaction(function () use ($entity, $old, $ctx) {
            // Блокируем строку в id_maps на случай параллельных upsert
            $ctx->lock();

            // Вызываем Importer, который внутри Context будет
            // делать $ctx->mapNewId($newUuid)
            $this->importer($entity)->import($ctx);
        });

        // 6) После транзакции newId уже записан в контекст
        if (empty($ctx->newId)) {
            unset($this->inProgress[$entity][$oldId]);
            throw new ImportException(
                "После импорта не получилось получить newId для {$entity}#{$oldId}"
            );
        }

        // 7) кешируем, сбрасываем inProgress и возвращаем
        $this->resolved[$entity][$oldId] = $ctx->newId;
        unset($this->inProgress[$entity][$oldId]);

        $this->logger->debug("{$entity}#{$oldId} импорт завершен");

        return $ctx->newId;
    }

    protected function mapKeyToOldDatabaseTable(string $key): string
    {
        return $this->map[$key]['table']
            ?? throw new ImportException("Неизвестна таблица для сущности {$key}");
    }

    public function mapKeyToModel(string $key): string
    {
        $modelClass = $this->map[$key]['model']
            ?? throw new ImportException("Неизвестна модель для сущности {$key}");

        if (!is_subclass_of($modelClass, \Illuminate\Database\Eloquent\Model::class)) {
            throw new ImportException("Класс {$modelClass} не является Eloquent-моделью");
        }

        return $modelClass;
    }


    protected function importer(string $key): ImporterInterface
    {
        $importerClass = $this->map[$key]['importer']
            ?? throw new ImportException("Импортёр для сущности {$key} не определён");

        if (!is_subclass_of($importerClass, ImporterInterface::class)) {
            throw new ImportException("Класс {$importerClass} не является импортёром");
        }

        return app($importerClass);
    }

    public function service(string $key): BaseComponentService
    {
        $serviceClass = $this->map[$key]['service']
            ?? throw new ImportException("Сервис для сущности {$key} не определён");

        if (!is_subclass_of($serviceClass, BaseComponentService::class)) {
            throw new ImportException("Класс {$serviceClass} не является сервисом компонента");
        }

        return app($serviceClass);
    }

    public function increaseCounter(string $entity): void
    {
        $this->importCount[$entity] = ($this->importCount[$entity] ?? 0) + 1;
    }

    public function getImportTotalCount(): int
    {
        return array_sum($this->importCount);
    }

    public function getImportCount(): string
    {
        $lines = [];
        foreach ($this->importCount as $entity => $count) {
            $lines[] = "* {$entity}: {$count}";
        }
        return implode("\n", $lines);
    }
}
