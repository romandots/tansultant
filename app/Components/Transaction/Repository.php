<?php

declare(strict_types=1);

namespace App\Components\Transaction;

use App\Models\Account;
use App\Models\Enum\TransactionTransferType;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Transaction make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Payment> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Transaction find(string $id)
 * @method Transaction findTrashed(string $id)
 * @method Transaction create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void restore(Transaction $record)
 * @method void forceDelete(Transaction $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Transaction::class,
            ['name']
        );
    }

    /**
     * @param Transaction $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->status = $dto->status;
        $record->type = $dto->type;
        $record->transfer_type = $dto->transfer_type;
        $record->customer_id = $dto->customer_id;
        $record->user_id = $dto->user_id;
        $record->account_id = $dto->account_id;
        $record->shift_id = $dto->shift_id;
        $record->external_id = $dto->external_id;
        $record->external_system = $dto->external_system;
        $record->confirmed_at = $dto->confirmed_at;
        $record->amount = $dto->amount;
        $record->name = $dto->name;
    }

    /**
     * @param Dto $dto
     * @param Account $fromAccount
     * @param Account $toAccount
     * @return Transaction[] [$fromAccountPayment, $toAccountPayment]
     */
    public function createInternalTransaction(Dto $dto, Account $fromAccount, Account $toAccount): array
    {
        $dto->transfer_type = TransactionTransferType::INTERNAL;
        //$dto->status = PaymentStatus::CONFIRMED;
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
     * @param Transaction $record
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