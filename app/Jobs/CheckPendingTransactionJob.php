<?php

namespace App\Jobs;

use App\Components\Loader;
use App\Models\Enum\TransactionStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Psr\Log\LoggerInterface;

class CheckPendingTransactionJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public const TIMEOUT = 5;

    protected string $transactionId;

    public function __construct(string $transactionId)
    {
        $this->transactionId = $transactionId;
    }

    public function handle(LoggerInterface $logger): void
    {
        $logger->info("Checking pending transaction #{$this->transactionId}...");
        $transaction = Loader::transactions()->find($this->transactionId);
        Loader::transactions()->checkPendingTransaction($transaction);

        if ($transaction->status === TransactionStatus::PENDING) {
            $logger->info("Transaction #{$this->transactionId} is still pending. Rescheduling the job...");
            dispatch(new self($this->transactionId))->delay(now()->addMinutes(self::TIMEOUT));
        } else {
            $logger->info("Transaction #{$this->transactionId} switched to {$transaction->status->value}");
        }
    }
}
