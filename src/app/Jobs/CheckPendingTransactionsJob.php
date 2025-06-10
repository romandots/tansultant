<?php

namespace App\Jobs;

use App\Components\Loader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Psr\Log\LoggerInterface;

class CheckPendingTransactionsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = app()->get(LoggerInterface::class);
    }

    public function handle(): void
    {
        $this->logger->info('Checking pending transactions...');
        $count = Loader::transactions()->checkPendingTransactions();
        $this->logger->info("{$count} transactions checked");
    }
}
