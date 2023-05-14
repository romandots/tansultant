<?php

namespace App\Jobs;

use App\Common\Traits\WithLogger;
use App\Components\Loader;
use App\Events\Payout\PayoutEvent;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePayoutReportJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WithLogger;

    public function __construct(
        private readonly Payout $payout,
        private readonly User $user,
    ) { }

    public function handle(): void
    {
        $this->debug('Handling GeneratePayoutReportJob job for payout #' . $this->payout->id);
        Loader::payouts()->generatePayoutReport($this->payout);
        PayoutEvent::checkedOut($this->payout, $this->user);
    }
}
