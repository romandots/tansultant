<?php

namespace App\Services\Import;

use App\Common\BaseComponentService;
use App\Console\Commands\AppInstall;
use App\Models\IdMap;
use App\Models\User;
use App\Services\Import\Contracts\ImporterInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\Exceptions\ImportSkippedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

class ImportManager
{
    protected readonly User $adminUser;

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
        try {
            $this->adminUser = User::query()->where('username', AppInstall::ADMIN_USERNAME)->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new ImportException('Не найден администратор для импорта');
        }
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string $entity
     * @param int|string $oldId
     * @param int $level
     * @return string
     * @throws ImportException
     * @throws ImportSkippedException
     */
    public function ensureImported(string $entity, int|string $oldId, int $level): string
    {
        $logPrefix = sprintf('%s%s#%s', str_repeat("\t", $level + 1), $entity, (string)$oldId);

        // 1) если уже в кеше — сразу отдать
        if (isset($this->resolved[$entity][$oldId])) {
            $this->logger->debug("{$logPrefix}: Уже импортирован - берем из кэша → #{$this->resolved[$entity][$oldId]}");
            return $this->resolved[$entity][$oldId];
        }

        // 2) если уже в БД — закэшировать и вернуть
        $existingUuid = IdMap::query()
            ->where('entity', $entity)
            ->where('old_id', (string)$oldId)
            ->value('new_id');
        if ($existingUuid) {
            $this->logger->debug("{$logPrefix}: Уже импортирован - берем из БД → #{$existingUuid}");
            return $this->resolved[$entity][$oldId] = $existingUuid;
        }

        try {
            // 3) защита от циклов
            if (!empty($this->inProgress[$entity][$oldId])) {
                throw new ImportException("Обнаружена циклическая зависимость", [
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
                throw new ImportException("Старая запись не найдена а таблице {$table}");
            }

            // 5) Готовим контекст и запускаем импор
            $ctx = new ImportContext($entity, $old, ++$level, $this, $this->logger, $this->adminUser);
            $this->importer($ctx->entity)->import($ctx);
        } catch (ImportException $importException) {
            unset($this->inProgress[$entity][$oldId]);
            $this->saveError($entity, $oldId, $importException->getMessage());
            throw $importException;
        }

        // 6) кешируем, сбрасываем inProgress и возвращаем
        unset($this->inProgress[$entity][$oldId]);
        if (empty($ctx->newId)) {
            throw new ImportException("Не удалось получить новый ID");
        }

        $this->resolved[$entity][$oldId] = $ctx->newId;
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


    public function saveNewId(string $entity, string|int $oldId, string $newId): void
    {
        $this->increaseCounter($entity);
        IdMap::query()->updateOrInsert(
            [
                'entity' => $entity,
                'old_id' => (string)$oldId,
            ],
            [
                'new_id' => $newId,
                'error' => null,
            ]
        );
    }

    public function saveError(string $entity, string|int $oldId, string $error): void
    {
        DB::statement(
            <<<'SQL'
        INSERT INTO id_maps (entity, old_id, new_id, error, attempts)
        VALUES (:entity, :old_id, NULL, :error, 1)
        ON CONFLICT (entity, old_id)
        DO UPDATE SET
            new_id   = NULL,
            error    = :error,
            attempts = id_maps.attempts + 1
    SQL,
            [
                'entity' => $entity,
                'old_id' => (string)$oldId,
                'error' => $error,
            ]
        );
    }
}
