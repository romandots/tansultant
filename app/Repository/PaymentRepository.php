<?php
/**
 * File: PaymentRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-28
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\Account;
use App\Models\Payment;
use Carbon\Carbon;

/**
 * Class PaymentRepository
 * @package App\Repository
 */
class PaymentRepository
{
    /**
     * @param \App\Repository\DTO\Payment $dto
     * @return Payment
     * @throws \Exception
     */
    public function create(DTO\Payment $dto): Payment
    {
        $payment = new Payment;
        $payment->id = \uuid();
        $payment->status = $dto->status;
        $payment->type = $dto->type;
        $payment->transfer_type = $dto->transfer_type;
        $payment->object_type = $dto->object_type;
        $payment->object_id = $dto->object_id;
        $payment->user_id = $dto->user_id;
        $payment->account_id = $dto->account_id;
        $payment->external_id = $dto->external_id;
        $payment->confirmed_at = $dto->confirmed_at;
        $payment->amount = $dto->amount;
        $payment->name = $dto->name;
        $payment->save();

        return $payment;
    }

    /**
     * @param DTO\Payment $dto
     * @param Account $fromAccount
     * @param Account $toAccount
     * @return array [$fromAccountPayment, $toAccountPayment]
     */
    public function createInternalTransaction(DTO\Payment $dto, Account $fromAccount, Account $toAccount): array
    {
        $dto->transfer_type = Payment::TRANSFER_TYPE_INTERNAL;
        $dto->status = Payment::STATUS_CONFIRMED;
        $dto->confirmed_at = Carbon::now();

        $firstDto = clone $dto;
        $secondDto = clone $dto;

        $firstDto->account_id = $fromAccount->id;
        $firstDto->amount = 0 - $dto->amount;

        $secondDto->account_id = $toAccount->id;
        $secondDto->amount = $dto->amount;

        return \DB::transaction(function () use ($secondDto, $firstDto) {
            $firstPayment = $this->create($firstDto);
            $secondPayment = $this->create($secondDto);

            $firstPayment->related_id = $secondPayment->id;
            $secondPayment->related_id = $firstPayment->id;

            $firstPayment->save();
            $secondPayment->save();

            return [$firstPayment, $secondPayment];
        });
    }
}
