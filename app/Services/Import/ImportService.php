<?php

namespace App\Services\Import;

use App\Services\Import\Maps\ObjectsMap;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class ImportService extends \App\Common\BaseService
{
    protected \Illuminate\Database\Connection $dbConnection;
    protected ProgressBar $bar;
    protected ?int $limit = null;
    protected int $batchSize = 100;
    protected array $imported = [];
    protected array $skipped = [];
    protected string $mapClass;
    protected array $mappers = [];

    public function __construct(
        public readonly \Illuminate\Console\Command $cli,
    ) {
        $this->connectToDatabase();
    }

    abstract protected function getTag(\stdClass $record): string;
    abstract protected function askDetails(): void;
    abstract protected function processImportRecord(\stdClass $record): Model;
    abstract protected function prepareImportQuery(): \Illuminate\Database\Query\Builder;

    protected function prepareDatabaseConfig(): void
    {
        $cachedConfig = Cache::get('db_config', []);
        $dbConfig = [
            'driver' => 'mysql',
            'host' => $this->cli->ask('Database host', $cachedConfig['host'] ?? 'localhost'),
            'port' => $this->cli->ask('Database port', $cachedConfig['port'] ?? '3306'),
            'database' => $this->cli->ask('Database name', $cachedConfig['database'] ?? 'my_database'),
            'username' => $this->cli->ask('Database username', $cachedConfig['username'] ?? 'root'),
            'password' => $this->cli->ask('Database password', $cachedConfig['password'] ?? 'root'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ];

        Config::set('database.connections.old_database', $dbConfig);
        Cache::set('db_config', $dbConfig);
    }

    protected function connectToDatabase(bool $useCached = false): void
    {
        $this->prepareDatabaseConfig($useCached);

        $this->cli->info('Connecting to database...');
        try {
            $this->dbConnection = DB::connection('old_database');
        } catch (\Exception $e) {
            $this->cli->error('Could not connect to database');
            exit;
        }
        $this->cli->info('New connection to old database established');
    }

    public function handleImportCommand(): void
    {
        $this->askDetails();

        $this->cli->newLine();
        $this->cli->info('Importing...');

        $this->import();
    }

    protected function validateImport(\stdClass $record): void
    {
        $mappedId = $this->mapped($record->id);
        if ($mappedId) {
            try {
                $collection = $this->getMapper()->getNewObjects();
                $recordExists = $collection->where('id', $mappedId)->first() !== null;
            } catch (\LogicException) {
                $recordExists = false;
            }

            if ($recordExists) {
                throw new Exceptions\ImportServiceException('Already exists and mapped');
            }

            $this->removeMapped($record->id);
        }
    }

    protected function importRecord(\stdClass $record): void
    {
        $tag = $this->getTag($record);

        try {
            $this->validateImport($record);
        } catch (Exceptions\ImportServiceException $e) {
            $this->skipped($tag, $e->getMessage());
            return;
        }

        try {
            $importedRecord = $this->processImportRecord($record);
        } catch (\Throwable $e) {
            if (isset($importedRecord) && $importedRecord instanceof Model) {
                $this->map($record->id, $importedRecord->id);
            }
            $this->skipped($tag, $e->getMessage());
            return;
        }

        $this->map($record->id, $importedRecord->id);
        $this->imported($importedRecord->id);
        $this->cli->info(sprintf("Imported: %d => %s", $record->id, $importedRecord->id));
        gc_collect_cycles();
    }

    protected function batchImport(iterable $records): void
    {
        foreach ($records as $record) {
            $this->importRecord($record);
            $this->bar->advance(1);
        }
    }

    protected function import(): void
    {
        $defaultLoggerChannel = Config::get('logging.default');
        Config::set('logging.default', 'gelf');

        $query = $this->prepareImportQuery();

        $totalRecords = $this->limit ?? $query->count();
        $this->startProgressBar($totalRecords);

        if ($this->limit) {
            $records = $query->get();
            $this->batchImport($records);
        } else {
            $query->chunk(
                $this->batchSize,
                fn ($records) => $this->batchImport($records)
            );
        }

        $this->bar->finish();
        $this->cli->newLine(2);

        if ($this->countSkipped()) {
            $this->cli->error('Skipped records:');
            foreach ($this->skipped as $tag => $reason) {
                $this->cli->comment($tag . ': ' . $reason);
            }
            $this->cli->newLine();
        }

        $this->cli->info('Import complete.');
        $this->cli->table(
            ['Imported records', 'Skipped  records', 'Total records'],
            [[$this->countImported(), $this->countSkipped(), $totalRecords]]
        );

        Config::set('logging.default', $defaultLoggerChannel);
    }

    public function getMapper(?string $mapperClass = null): ObjectsMap
    {
        $mapperClass ??= $this->mapClass;
        if (!isset($this->mappers[$mapperClass])) {
            if (!isset($mapperClass)) {
                throw new \Exception('Map class is not set');
            }

            $this->mappers[$mapperClass] = app()->get($mapperClass);
            if ($this->mappers[$mapperClass] instanceof ObjectsMap) {
                $this->mappers[$mapperClass]
                    ->setCli($this->cli)
                    ->setDbConnection($this->dbConnection)
                    ->loadMap();
            }
        }

        return $this->mappers[$mapperClass];
    }

    protected function buildMap(): void
    {
        $this->getMapper()->buildMap();
    }

    protected function mapped(int|string $oldId): ?string
    {
        return $this->getMapper()->mapped($oldId);
    }

    protected function map(int|string $oldId, string $newId): void
    {
        $this->getMapper()->map($oldId, $newId);
    }

    private function removeMapped(int|string $oldId): void
    {
        $this->getMapper()->removeMapped($oldId);
    }

    protected function startProgressBar(int $totalRecords): void
    {
        $this->bar = $this->cli->getOutput()->createProgressBar($totalRecords);
        $this->bar->start();
    }

    protected function countImported(): int
    {
        return count($this->imported);
    }

    protected function countSkipped(): int
    {
        return count($this->skipped);
    }

    protected function skipped(string $tag, string $reason): void
    {
        $this->skipped[$tag] = $reason;
    }

    protected function imported(string $id): void
    {
        $this->imported[] = $id;
    }

    protected function nextNumber(): int
    {
        return $this->getMapper()->nextNumber();
    }
}