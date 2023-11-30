<?php

declare(strict_types=1);

namespace App\Components\Transaction;

use App\Adapters\Banks\Contracts\QrCode;
use App\Adapters\Banks\TochkaBank\TochkaBankSbpAdapter;
use App\Common\Contracts;
use App\Components\Loader;
use App\Components\Shift\Exceptions\UserHasNoActiveShift;
use App\Events\Account\AccountEvent;
use App\Events\Shift\ShiftEvent;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionTransferType;
use App\Models\Enum\TransactionType;
use App\Models\Shift;
use App\Models\Transaction;
use App\Services\Permissions\TransactionsPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    protected \App\Components\Account\Facade $accounts;

    public function __construct()
    {
        parent::__construct(
            Transaction::class,
            Repository::class,
            Dto::class,
            null
        );
        $this->accounts = \app(\App\Components\Account\Facade::class);
    }

    /**
     * Creates manual transaction
     * @param Dto $dto
     * @return Transaction
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        $user = $dto->getUser();
        if (null === $user) {
            throw new \LogicException('user_is_not_defined');
        }

        $customer = $dto->customer_id ? Loader::customers()->findById($dto->customer_id) : null;

        $shift = $this->getUserShift($user);
        $dto->shift_id = $shift?->id;
        $dto->name = $customer ? $this->generateDepositName($customer) : $dto->name;
        $dto->type = TransactionType::MANUAL;
        $dto->status = TransactionStatus::PENDING;

        /** @var Transaction $transaction */
        $transaction = parent::create($dto);

        match ($transaction->transfer_type) {
            TransactionTransferType::CARD,
            TransactionTransferType::CASH,
            TransactionTransferType::INTERNAL => $this->setTransactionConfirmed($transaction),
            TransactionTransferType::CODE => $this->getQrCodeAndSendLinkToCustomer($transaction),
            TransactionTransferType::ONLINE => throw new \LogicException('Not implemented yet'),
        };

        $this->dispatchEventsUponCreatingNewTransaction($transaction);

        return $transaction;
    }

    public function setTransactionConfirmed(Transaction $transaction): void
    {
        $toStatus = TransactionStatus::CONFIRMED;
        if ($transaction->status === $toStatus) {
            throw new Exceptions\AlreadyInStatusExceptions($toStatus);
        }

        DB::beginTransaction();

        if ($transaction->customer) {
            $this->createCredit($transaction);
        }

        if ($transaction->shift) {
            Loader::shifts()->updateTotalIncome($transaction->shift);
        }

        $this->getRepository()->setStatus($transaction, $toStatus, ['confirmed_at']);

        DB::commit();

        $this->dispatchEventsUponConfirmingTransaction($transaction);
    }

    public function setTransactionCanceled(Transaction $transaction): void
    {
        $toStatus = TransactionStatus::CANCELED;
        if ($transaction->status === $toStatus) {
            throw new Exceptions\AlreadyInStatusExceptions($toStatus);
        }

        $this->getRepository()->setStatus($transaction, $toStatus, ['canceled_at']);
    }

    public function setTransactionExpired(Transaction $transaction): void
    {
        $toStatus = TransactionStatus::EXPIRED;
        if ($transaction->status === $toStatus) {
            throw new Exceptions\AlreadyInStatusExceptions($toStatus);
        }

        $this->getRepository()->setStatus($transaction, $toStatus);
    }

    protected function createCredit(Transaction $transaction): Credit
    {
        return Loader::credits()->createIncome(
            $transaction->customer, $transaction->amount, $transaction->name, $transaction->user, $transaction
        );
    }

    protected function generateDepositName(Customer $customer): string
    {
        return trans('credit.deposit', ['customer' => $customer->name]);
    }

    public function createPayoutTransaction(
        \App\Models\Payout $payout,
        \App\Models\Account $account,
        \App\Models\User $user
    ): Transaction {
        $dto = new Dto($user);
        $dto->amount = 0 - (int)$payout->amount;
        $dto->account_id = $account->id;
        $dto->name = $payout->name;
        $dto->user_id = $user->id;
        $dto->status = TransactionStatus::CONFIRMED;
        $dto->type = \App\Models\Enum\TransactionType::AUTO;
        $dto->transfer_type = \App\Models\Enum\TransactionTransferType::CASH;
        $dto->confirmed_at = now();
        $dto->shift_id = null;

        return parent::create($dto);
    }

    protected function dispatchEventsUponCreatingNewTransaction(Transaction $transaction): void
    {
        AccountEvent::transactionsUpdated($transaction->account);
        if ($transaction->shift) {
            ShiftEvent::transactionsUpdated($transaction->shift);
        }
    }

    protected function dispatchEventsUponConfirmingTransaction(Transaction $transaction): void
    {
        AccountEvent::transactionsUpdated($transaction->account);
        if ($transaction->shift) {
            ShiftEvent::transactionsUpdated($transaction->shift);
        }
    }

    protected function getUserShift(\App\Models\User $user): ?Shift
    {
        /** @var Shift $shift */
        $shift = $user->load('active_shift')->active_shift;

        if ($shift && $shift->isClosed()) {
            $shift = null;
        }

        if (null === $shift && !$user->can(TransactionsPermission::CREATE_WITHOUT_SHIFT)) {
            throw new UserHasNoActiveShift($user);
        }

        return $shift;
    }

    protected function getQrCodeAndSendLinkToCustomer(Transaction $transaction): QrCode
    {
        $qrCode = $this->generateQrCode($transaction);
        $this->sendLinkToCustomer($transaction, $qrCode);

        return $qrCode;
    }

    protected function checkValidityForQrCodeOperations(Transaction $transaction): void
    {
        if ($transaction->transfer_type !== TransactionTransferType::CODE) {
            $this->error('Транзакция не в типе "Код"');
            throw new Exceptions\NotInTransferTypeException(TransactionTransferType::CODE);
        }

        if ($transaction->status !== TransactionStatus::PENDING) {
            $this->error('Транзакция не в статусе "Ожидает подтверждения"');
            throw new Exceptions\NotInStatusExceptions(TransactionStatus::PENDING);
        }
    }

    protected function generateQrCode(Transaction $transaction): QrCode
    {
        $this->debug('Генерируем QR-код для оплаты');

        $this->checkValidityForQrCodeOperations($transaction);

        $qrCode = $this->getBankClient()->registerQrCode(
            amount: $transaction->amount,
            comment: $transaction->name,
        );

        $transaction->external_id = $qrCode->id;
        $transaction->external_system = $qrCode->getSystem();
        $this->getRepository()->save($transaction);

        $this->debug('Сохранили внешний ID и внешнюю систему в транзакции', [
            'id' => $transaction->id,
            'external_id' => $transaction->external_id,
            'external_system' => $transaction->external_system,
        ]);

        return $qrCode;
    }

    public function getQrCode(Transaction $transaction): QrCode
    {
        $this->debug('Запрашиваем у банка QR-код по ID', [
            'id' => $transaction->id,
            'external_id' => $transaction->external_id,
            'external_system' => $transaction->external_system,
        ]);

        $this->checkValidityForQrCodeOperations($transaction);

        return $this->getBankClient()->getQrCode($transaction->external_id);
    }

    protected function getBankClient(): TochkaBankSbpAdapter
    {
        return app()->get(TochkaBankSbpAdapter::class);
    }

    protected function sendLinkToCustomer(Transaction $transaction, QrCode $qrCode): void
    {
        $this->debug('Отправляем ссылку для оплаты', [
            'customer_id' => $transaction->customer->id,
            'person_id' => $transaction->customer->person_id,
            'transaction_id' => $transaction->id,
        ]);

        $message = trans('transaction.messages.qr_code_message', ['link' => $qrCode->getLink()]);
        Loader::notifications()->notify($transaction->customer->person, $message);
    }

}