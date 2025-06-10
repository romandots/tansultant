<?php

declare(strict_types=1);

namespace App\Components\Transaction;

use App\Adapters\Banks\Contracts\QrCode;
use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Account;
use App\Models\Payout;
use App\Models\Transaction;
use App\Models\User;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Payment> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Payment> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Transaction create(Dto $dto, array $relations = [])
 * @method \App\Models\Transaction find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Transaction findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Transaction findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function createPayoutTransaction(Payout $payout, Account $account, User $user): Transaction
    {
        return $this->getService()->createPayoutTransaction($payout, $account, $user);
    }

    public function getQrCode(Transaction $transaction): QrCode
    {
        return $this->getService()->getQrCode($transaction);
    }

    public function checkPendingTransactions(): int
    {
        return $this->getService()->checkPendingTransactions();
    }

    public function checkPendingTransaction(Transaction $transaction): void
    {
        $this->getService()->checkPendingTransaction($transaction);
    }

    public function sendPaymentLink(Transaction $transaction): QrCode
    {
        return $this->getService()->getQrCodeAndSendLinkToCustomer($transaction);
    }

    public function cancelAllPendingShiftTransactions(\App\Models\Shift $shift): void
    {
        $this->getService()->cancelAllPendingShiftTransactions($shift);
    }
}