<?php

declare(strict_types=1);

namespace App\Components\Visit;

use App\Common\DTO\SearchFilterDto;
use App\Http\Requests\ManagerApi\DTO\SearchVisitsFilterDto;
use App\Models\Enum\VisitEventType;
use App\Models\Subscription;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Visit make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Visit> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Visit find(string $id)
 * @method Visit findTrashed(string $id)
 * @method Visit create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Visit $record)
 * @method void restore(Visit $record)
 * @method void forceDelete(Visit $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            modelClass: Visit::class,
            searchableAttributes: ['name'],
        );
    }

    public function updatePayment(Visit $visit, ?\App\Models\Payment $payment = null, ?Subscription $subscription = null): void
    {
        $visit->payment_id = $payment?->id;
        $visit->subscription_id = $subscription?->id;
        $this->save($visit);
    }

    protected function getFilterQuery(
        SearchFilterDto $filter,
        array $relations = [],
        array $countRelations = []
    ): \Illuminate\Database\Eloquent\Builder {
        $query = parent::getFilterQuery($filter, $relations, $countRelations);

        assert($filter instanceof SearchVisitsFilterDto);

        if (null !== $filter->date) {
            $query
                ->whereRaw('DATE(starts_at) = ?', [$filter->date]);
        }

        if (null !== $filter->lesson_id) {
            $query = $query
                ->where('event_type', VisitEventType::LESSON->value)
                ->where('event_id', $filter->lesson_id);
        }

        return $query
            ->distinct()
            ->orderBy('created_at', 'asc');
    }

    /**
     * @param Visit $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->student_id = $dto->student_id;
        $record->manager_id = $dto->getUser()->id;
        $record->event_type = $dto->event_type;
        $record->event_id = $dto->event_id;
        $record->payment_type = $dto->payment_type;
        $record->payment_id = $dto->payment_id;
        $record->subscription_id = $dto->subscription_id;
        $record->price = $dto->price;
    }

    /**
     * @param string $studentId
     * @param string $eventId
     * @return Visit
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function findByStudentIdAndEventId(string $studentId, string $eventId): Visit
    {
        return $this->getQuery()
            ->where('student_id', $studentId)
            ->where('event_id', $eventId)
            ->firstOrFail();
    }
}