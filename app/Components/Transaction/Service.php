<?php

declare(strict_types=1);

namespace App\Components\Transaction;

use App\Adapters\Banks\Contracts\QrCode;
use App\Adapters\Banks\Contracts\SbpAdapter;
use App\Adapters\Banks\TochkaBank\Exceptions\TochkaBankAdapterException;
use App\Common\Contracts;
use App\Components\Loader;
use App\Components\Shift\Exceptions\UserHasNoActiveShift;
use App\Components\Transaction\Exceptions\Exception;
use App\Events\Account\AccountEvent;
use App\Events\Shift\ShiftEvent;
use App\Jobs\CheckPendingTransactionJob;
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

        DB::beginTransaction();

        /** @var Transaction $transaction */
        $transaction = parent::create($dto);

        match ($transaction->transfer_type) {
            TransactionTransferType::CARD,
            TransactionTransferType::CASH,
            TransactionTransferType::INTERNAL => $this->setTransactionConfirmed($transaction),
            TransactionTransferType::CODE => $this->handleCodeTransaction($transaction),
            TransactionTransferType::ONLINE => throw new \LogicException('Not implemented yet'),
        };

        DB::commit();

        $this->dispatchEventsUponCreatingNewTransaction($transaction);

        return $transaction;
    }

    protected function handleCodeTransaction(Transaction $transaction): void
    {
        try {
            $this->getQrCodeAndSendLinkToCustomer($transaction);
        } catch (\Throwable $e) {
            $this->error('Ошибка при создании или отправке QR-кода транзакции', [
                'error_message' => $e->getMessage(),
                'transaction_id' => $transaction->id,
                'exception' => $e
            ]);
            throw $e;
        }

        try {
            $this->dispatchCheckPendingTransactionJob($transaction);
        } catch (\Throwable $e) {
            $this->error('Ошибка при создании задачи проверки транзакции', [
                'error_message' => $e->getMessage(),
                'transaction_id' => $transaction->id,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    public function setTransactionConfirmed(Transaction $transaction): void
    {
        $this->debug('Подтверждаем транзакцию', [
            'transaction_id' => $transaction->id,
            'transaction_status' => $transaction->status,
        ]);

        $fromStatus = TransactionStatus::PENDING;
        $toStatus = TransactionStatus::CONFIRMED;
        if ($transaction->status !== $fromStatus) {
            throw new Exceptions\NotInStatusExceptions($fromStatus);
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
        $this->debug('Отменяем транзакцию', [
            'transaction_id' => $transaction->id,
            'transaction_status' => $transaction->status,
        ]);

        $fromStatus = TransactionStatus::PENDING;
        $toStatus = TransactionStatus::CANCELED;
        if ($transaction->status !== $fromStatus) {
            throw new Exceptions\NotInStatusExceptions($fromStatus);
        }

        $this->getRepository()->setStatus($transaction, $toStatus, ['canceled_at']);
        $this->dispatchEventsUponCancelingTransaction($transaction);
    }

    public function setTransactionExpired(Transaction $transaction): void
    {
        $this->debug('Отмечаем транзакцию как просроченную', [
            'transaction_id' => $transaction->id,
            'transaction_status' => $transaction->status,
        ]);

        $fromStatus = TransactionStatus::PENDING;
        $toStatus = TransactionStatus::EXPIRED;
        if ($transaction->status !== $fromStatus) {
            throw new Exceptions\NotInStatusExceptions($fromStatus);
        }

        $this->getRepository()->setStatus($transaction, $toStatus);
        $this->dispatchEventsUponExpiringTransaction($transaction);
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

    public function getQrCodeAndSendLinkToCustomer(Transaction $transaction): QrCode
    {
        $qrCode = $this->generateQrCode($transaction);
        $this->sendLinkToCustomer($transaction, $qrCode);

        return $qrCode;
    }

    protected function assertTransactionIsCodeTypeAndPending(Transaction $transaction): void
    {
        if (!$transaction->assert(TransactionTransferType::CODE)) {
            $this->error('Транзакция не в типе "Код"');
            throw new Exceptions\NotInTransferTypeException(TransactionTransferType::CODE);
        }

        if (!$transaction->assert(TransactionStatus::PENDING)) {
            $this->error('Транзакция не в статусе "Ожидает подтверждения"');
            throw new Exceptions\NotInStatusExceptions(TransactionStatus::PENDING);
        }
    }

    protected function generateQrCode(Transaction $transaction): QrCode
    {
        $this->debug('Генерируем QR-код для оплаты');

        $this->assertTransactionIsCodeTypeAndPending($transaction);

        $sbpAdapter = $this->getSbpClient();

        try {
            $qrCode = $sbpAdapter->registerQrCode($transaction);
        } catch (TochkaBankAdapterException $e) {
            $this->error('Не удалось зарегистрировать QR-код', [
                'transaction_id' => $transaction->id,
                'error_message' => $e->getMessage(),
            ]);
            throw Exceptions\QrCodeException::becauseOfTochkaBankAdapterException($e);
        }

        $transaction->external_id = $qrCode->id;
        $transaction->external_system = $sbpAdapter->externalSystemName();
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
            'transaction_id' => $transaction->id,
            'external_id' => $transaction->external_id,
            'external_system' => $transaction->external_system,
        ]);

        $this->assertTransactionIsCodeTypeAndPending($transaction);

        return $this->getSbpClient()->getQrCode($transaction->external_id);
    }

    protected function getSbpClient(): SbpAdapter
    {
        $isMockEnabled = config('tochkabank.enable_mock');
        $sbpClientClass = config($isMockEnabled ? 'transactions.sbp_client_mock' : 'transactions.sbp_client');
        $client = app()->get($sbpClientClass);

        if (!($client instanceof SbpAdapter)) {
            throw new Exceptions\Exception($sbpClientClass . 'is not implemented');
        }

        return $client;
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

    protected function dispatchCheckPendingTransactionJob(Transaction $transaction): void
    {
        if ($transaction->status !== TransactionStatus::PENDING) {
            $this->debug('Транзакция не в статусе "Ожидает подтверждения"', [
                'transaction_id' => $transaction->id,
                'status' => $transaction->status,
            ]);

            throw new Exceptions\NotInStatusExceptions(TransactionStatus::PENDING);
        }

        $this->debug('Запускаем мониторинг статуса транзакции', [
            'transaction_id' => $transaction->id,
            'external_id' => $transaction->external_id,
            'external_system' => $transaction->external_system,
        ]);

        dispatch(new CheckPendingTransactionJob($transaction->id));
    }

    public function checkPendingTransactions(): int
    {
        return $this->getRepository()
            ->getPendingTransactions()
            ->each(function (Transaction $transaction) {
                $this->checkPendingTransaction($transaction);
            })
            ->count();
    }

    public function checkPendingTransaction(Transaction $transaction): void
    {
        if ($transaction->status !== TransactionStatus::PENDING) {
            $this->debug('Транзакция не в статусе "Ожидает подтверждения"', [
                'transaction_id' => $transaction->id,
                'status' => $transaction->status,
            ]);

            throw new Exceptions\NotInStatusExceptions(TransactionStatus::PENDING);
        }

        match ($transaction->transfer_type) {
            TransactionTransferType::CODE => $this->checkPendingQrCodeTransaction($transaction),
            default => throw new Exceptions\Exception('Transaction type is unsupported'),
        };
    }

    protected function checkPendingQrCodeTransaction(Transaction $transaction): void
    {
        $this->debug('Проверяем статус транзакции', [
            'transaction_id' => $transaction->id,
            'external_id' => $transaction->external_id,
            'external_system' => $transaction->external_system,
        ]);

        try {
            $qrCode = $this->getSbpClient(
                config('')
            )->getQrCode($transaction->external_id);
        } catch (TochkaBankAdapterException $e) {
            $this->error('Ошибка при запросе статуса транзакции', [
                'transaction_id' => $transaction->id,
                'external_id' => $transaction->external_id,
                'external_system' => $transaction->external_system,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }

        match (true) {
            $qrCode->isExpired() => $this->setTransactionExpired($transaction),
            $qrCode->isPaid() => $this->setTransactionConfirmed($transaction),
            default => $this->debug('Транзакция не изменилась', [
                'transaction_id' => $transaction->id,
                'external_id' => $transaction->external_id,
                'external_system' => $transaction->external_system,
                'transaction_status' => $transaction->status->value,
            ]),
        };
    }

    protected function dispatchEventsUponCancelingTransaction(Transaction $transaction): void
    {
        AccountEvent::transactionsUpdated($transaction->account);
        if ($transaction->shift) {
            ShiftEvent::transactionsUpdated($transaction->shift);
        }
    }

    protected function dispatchEventsUponExpiringTransaction(Transaction $transaction): void
    {
        AccountEvent::transactionsUpdated($transaction->account);
        if ($transaction->shift) {
            ShiftEvent::transactionsUpdated($transaction->shift);
        }
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
}