<?php

declare(strict_types=1);

namespace App\Components\Tariff;

use App\Models\Course;
use App\Models\Enum\CourseStatus;
use App\Models\Enum\TariffStatus;
use App\Models\Tariff;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Tariff make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Tariff> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Tariff find(string $id)
 * @method Tariff findTrashed(string $id)
 * @method Tariff create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Tariff $record)
 * @method void restore(Tariff $record)
 * @method void forceDelete(Tariff $record)
 * @mixin \App\Common\BaseComponentRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            Tariff::class,
            ['name']
        );
    }

    /**
     * @param Tariff $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
        $record->price = $dto->price;
        $record->prolongation_price = $dto->prolongation_price;
        $record->courses_limit = $dto->courses_limit;
        $record->visits_limit = $dto->visits_limit;
        $record->days_limit = $dto->days_limit;
        $record->holds_limit = $dto->holds_limit;
        $record->status = $dto->status;
    }

    /**
     * @param Tariff $tariff
     * @param iterable<Course> $courses
     * @return void
     */
    public function attachCourses(Tariff $tariff, iterable $courses): void
    {
        foreach ($courses as $course) {
            if ($course->status === CourseStatus::DISABLED) {
                throw new Exceptions\CannotAttachDisabledCourse($course);
            }
            $tariff->courses()->attach($course->id);
        }
    }

    /**
     * @param Tariff $tariff
     * @param iterable<Course> $courses
     * @return void
     */
    public function detachCourses(Tariff $tariff, iterable $courses): void
    {
        foreach ($courses as $course) {
            $tariff->courses()->detach($course->id);
        }
    }

    public function updateStatus(Tariff $record, TariffStatus $status): void
    {
        $record->status = $status;
        $this->save($record);
    }
}