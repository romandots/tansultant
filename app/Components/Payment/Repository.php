<?php

declare(strict_types=1);

namespace App\Components\Payment;

use App\Models\Account;
use App\Models\Enum\PaymentStatus;
use App\Models\Enum\PaymentTransferType;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Payment make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Payment> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Payment find(string $id)
 * @method Payment findTrashed(string $id)
 * @method Payment create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void restore(Payment $record)
 * @method void forceDelete(Payment $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Payment::class,
            ['name']
        );
    }

    /**
     * @param Payment $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->status = $dto->status;
        $record->type = $dto->type;
        $record->transfer_type = $dto->transfer_type;
        $record->object_type = $dto->object_type;
        $record->object_id = $dto->object_id;
        $record->user_id = $dto->user_id;
        $record->account_id = $dto->account_id;
        $record->external_id = $dto->external_id;
        $record->confirmed_at = $dto->confirmed_at;
        $record->amount = $dto->amount;
        $record->name = $dto->name;
    }

    /**
     * @param Dto $dto
     * @param Account $fromAccount
     * @param Account $toAccount
     * @return Payment[] [$fromAccountPayment, $toAccountPayment]
     */
    public function createInternalTransaction(Dto $dto, Account $fromAccount, Account $toAccount): array
    {
        $dto->transfer_type = PaymentTransferType::INTERNAL;
        $dto->status = PaymentStatus::CONFIRMED;
        $dto->confirmed_at = Carbon::now();

        $firstDto = clone $dto;
        $secondDto = clone $dto;

        $firstDto->account_id = $fromAccount->id;
        $firstDto->amount = 0 - $dto->amount;

        $secondDto->account_id = $toAccount->id;
        $secondDto->amount = $dto->amount;

        return \DB::transaction(function () use ($secondDto, $firstDto) {
            // create two transactions
            $firstPayment = $this->create($firstDto);
            $secondPayment = $this->create($secondDto);

            // bind it to each other
            $firstPayment->related_id = $secondPayment->id;
            $firstPayment->save();
            $secondPayment->related_id = $firstPayment->id;
            $secondPayment->save();

            return [$firstPayment, $secondPayment];
        });
    }

    /**
     * @param Payment $record
     * @return void
     */
    public function delete(Model $record): void
    {
        if (null === $record->related_payment) {
            parent::delete($record);
            return;
        }

        \DB::transaction(function () use ($record) {
            $related = $record->related_payment;

            $related->related_id = null;
            $record->related_id = null;

            $related->save();
            $record->save();

            parent::delete($related);
            parent::delete($record);
        });
    }

}