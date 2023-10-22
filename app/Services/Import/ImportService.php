<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class ImportService extends \App\Common\BaseService
{
    protected \Illuminate\Database\Connection $dbConnection;
    protected ProgressBar $bar;
    protected int $batchSize = 100;
    protected array $imported = [];
    protected array $skipped = [];

    public function __construct(
        public readonly \Illuminate\Console\Command $cli
    ) { }

    abstract public function handleImportCommand(): void;
    abstract protected function importRecord(\stdClass $record): void;
    abstract protected function prepareImportQuery(): \Illuminate\Database\Query\Builder;

    public function import(): void
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
        $this->cli->error('Skipped records:');
        foreach ($this->skipped as $tag => $reason) {
            $this->cli->comment($tag . ': ' . $reason);
        }
        $this->cli->newLine();
        $this->cli->info('Import complete.');
        $this->cli->table(['Imported records', 'Skipped  records'], [[$this->countImported(), $this->countSkipped()]]);

        Config::set('logging.default', $defaultLoggerChannel);
    }

    protected function batchImport(iterable $records): void
    {
        foreach ($records as $record) {
            $this->importRecord($record);
            $this->bar->advance(1);
        }
    }

    protected function connectToDatabase(): void
    {
        $this->prepareDatabaseConfig();

        $this->cli->info('Connecting to database...');
        try {
            $this->dbConnection = DB::connection('old_database');
        } catch (\Exception $e) {
            $this->cli->error('Could not connect to database');
            exit;
        }
        $this->cli->info('New connection to old database established');
    }

    /**
     * @return void
     */
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
}