<?php

namespace App\Console\Commands;

use App\Components\Loader;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class TransactionsCheckPending extends Command implements Isolatable
{
    protected $signature = 'transactions:check-pending {id?}';

    protected $description = 'Check all pending transactions statuses';

    public function handle()
    {
        $this->info('ID: ' . $this->argument('id'));

        $this->info('Checking pending transactions...');
        $count = Loader::transactions()->checkPendingTransactions();
        $this->info('Done!');
        $this->info("{$count} transactions checked");
    }
}
