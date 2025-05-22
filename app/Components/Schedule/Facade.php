<?php

declare(strict_types=1);

namespace App\Components\Schedule;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Http\Requests\ManagerApi\DTO\ScheduleOnDate;
use App\Models\Schedule;
use Illuminate\Support\Collection;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Schedule> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Schedule> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Schedule create(Dto $dto, array $relations = [])
 * @method \App\Models\Schedule find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Schedule findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Schedule findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function findOrCreate(Dto $dto): Schedule
    {
        return $this->getService()->findOrCreate($dto);
    }

    public function findByDto(Dto $dto): Schedule
    {
        return $this->getRepository()->findByDto($dto);
    }

    public function getSchedulesForCourseOnDate(string $courseId, \Carbon\Carbon $date): Collection
    {
        return $this->getRepository()->getSchedulesForCourseOnDate($courseId, $date);
    }

    public function getSchedulesOnDate(\Carbon\Carbon $date): Collection
    {
        return $this->getRepository()->getSchedulesOnDate($date);
    }

    /**
     * @param ScheduleOnDate $scheduleOnDate
     * @param array $relations (optional)
     * @return Collection<Schedule>
     */
    public function getSchedulesForDateWithRelations(ScheduleOnDate $scheduleOnDate, array $relations = []): Collection
    {
        return $this->getRepository()->getSchedulesForDateWithRelations($scheduleOnDate, $relations);
    }
}