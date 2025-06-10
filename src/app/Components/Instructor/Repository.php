<?php

declare(strict_types=1);

namespace App\Components\Instructor;

use App\Models\Enum\LessonStatus;
use App\Models\Instructor;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Instructor make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Instructor> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Instructor find(string $id)
 * @method Instructor findTrashed(string $id)
 * @method Instructor create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Instructor $record)
 * @method void restore(Instructor $record)
 * @method void forceDelete(Instructor $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Instructor::class,
            ['name']
        );
    }

    /**
     * @param Instructor $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->person_id = $dto->person_id;
        $record->description = $dto->description;
        $record->display = $dto->display;
        $record->status = $dto->status ?? InstructorStatus::HIRED;
    }

    public function getInvolvedInstructorsIdsForBranchAndPeriod(
        string $branchId,
        \Carbon\Carbon $from,
        \Carbon\Carbon $to
    ): array {
        $table = Lesson::TABLE;
        $sql = <<<SQL
SELECT DISTINCT instructor_id AS id
FROM {$table}
WHERE {$table}.branch_id = ? 
    AND {$table}.starts_at >= ?
    AND {$table}.starts_at < ?
    AND {$table}.status = ?
    AND {$table}.id NOT IN (
        SELECT lesson_id FROM payout_has_lessons
    )
    AND {$table}.instructor_id IS NOT NULL
SQL;

        $results = \DB::select($sql, [
            $branchId,
            $from,
            $to,
            LessonStatus::CLOSED->value,
        ]);

        return \array_map(fn ($item) => $item->id, $results);
    }
}