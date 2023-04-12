<?php

declare(strict_types=1);

namespace App\Components\Payout;

use App\Components\Loader;
use App\Models\Enum\PayoutStatus;
use App\Models\Formula;
use App\Models\Lesson;
use App\Models\Payout;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Payout make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Payout> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Payout find(string $id)
 * @method Payout findTrashed(string $id)
 * @method Payout create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Payout $record)
 * @method void restore(Payout $record)
 * @method void forceDelete(Payout $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Payout::class,
            ['name']
        );
    }

    /**
     * @param Payout $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->branch_id = $dto->branch_id;
        $record->instructor_id = $dto->instructor_id;
        $record->status = $dto->status;
        $record->period_from = $dto->period_from;
        $record->period_to = $dto->period_to;
    }

    public function setPrepared(Payout $payout): void
    {
        $this->updateStatus($payout, PayoutStatus::PREPARED);
    }

    public function setPaid(Payout $payout): void
    {
        $this->updateStatus($payout, PayoutStatus::PAID, ['paid_at']);
    }

    public function attachLessonWithFormula(Payout $payout, Lesson $lesson, Formula $formula): void
    {
        $payload = [
            'created_at' => Carbon::now(),
            'formula_id' => $formula->id,
            'equation' => $formula->equation,
            'amount' => Loader::formulas()->calculateLessonPayoutAmount($lesson, $formula),
        ];
        try {
            $payout->lessons()->attach($lesson->id, $payload);
        } catch (\Exception) {}
    }

    public function detachLesson(Payout $payout, Lesson $lesson): void
    {
        $payout->lessons()->detach($lesson->id);
    }

    public function updateTotalAmount(Payout $payout): void
    {
        $payout->amount = $payout->lessons()->sum('amount');
        $this->save($payout);
    }

    public function setTransaction(Payout $payout, \App\Models\Transaction $transaction): void
    {
        $payout->transaction_id = $transaction->id;
        $this->save($payout);
    }
}