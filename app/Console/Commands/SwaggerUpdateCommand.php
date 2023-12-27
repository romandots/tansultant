<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class SwaggerUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Swagger documentation';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $process = Process::fromShellCommandline(
            'vendor/bin/openapi --output swagger/swagger.json app/Http/Controllers',
            '/app',
        );

        $this->info('Updating swagger/swagger.json file');
        $process->run(function ($type, $buffer) {
            $this->line($buffer);
        });
    }
}
