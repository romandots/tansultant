<?php
declare(strict_types=1);

namespace App\Events\Account;

use App\Events\BaseEvent;
use App\Models\Account;
use JetBrains\PhpStorm\Pure;

abstract class AccountEvent extends BaseEvent
{
    public function __construct(
        protected Account $account,
    ) { }

    #[Pure] public function getChannelName(): string
    {
        return 'account.' . $this->getAccountId();
    }

    /**
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->account->id;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    public static function transactionsUpdated(Account $account): void
    {
        AccountTransactionsUpdatedEvent::dispatch($account);
    }
}
