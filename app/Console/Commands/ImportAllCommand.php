<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportAllCommand extends \Illuminate\Console\Command
{
    protected $signature = 'import:all';
    protected $description = 'Import everything from old database in proper order';
    protected array $commands = [
        ImportBranchesCommand::class,
        ImportClassroomsCommand::class,
        ImportStudentsCommand::class,
        ImportTariffsCommand::class,
        ImportCoursesCommand::class,
        ImportSubscriptionsCommand::class,
    ];

    public function handle(): void
    {
        foreach ($this->commands as $commandName) {
            $command = new $commandName();
            if (!($command instanceof Command)) {
                throw new \LogicException($commandName . ' is not a command class');
            }

            $this->info($command->getDescription());
            $this->newLine();
            $this->runCommand($commandName, [], $this->getOutput());
        }
    }
}