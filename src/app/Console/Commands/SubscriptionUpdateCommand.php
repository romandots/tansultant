<?php

namespace App\Console\Commands;

use App\Components\Loader;
use Illuminate\Console\Command;

class SubscriptionUpdateCommand extends Command
{
    protected $signature = 'subscription:update';
    protected $description = 'Update subscriptions statuses';

    public function handle(): void
    {
        $this->info('Updating subscriptions statuses');
        $updatedCount = Loader::subscriptions()->updateSubscriptionsStatuses();
        $this->info("Done! {$updatedCount} subscriptions updated");
    }
}
