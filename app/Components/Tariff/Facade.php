<?php

declare(strict_types=1);

namespace App\Components\Tariff;

use App\Common\BaseComponentFacade;
use App\Common\DTO\IdsDto;
use App\Common\DTO\ShowDto;
use App\Components\Loader;
use App\Models\Tariff;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Tariff> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Tariff> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Tariff create(Dto $dto, array $relations = [])
 * @method \App\Models\Tariff find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Tariff findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Tariff findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    /**
     * @param IdsDto $dto
     * @return Tariff
     * @throws \Exception
     */
    public function findAndAttachCourses(IdsDto $dto): Tariff
    {
        $tariff = $this->getRepository()->find($dto->id);
        $courses = Loader::courses()->getMany($dto->relations_ids);
        $this->getService()->attachCourses($tariff, $courses, $dto->user);

        return $tariff->load('courses.instructor');
    }

    /**
     * @param IdsDto $dto
     * @return Tariff
     * @throws \Exception
     */
    public function findAndDetachCourses(IdsDto $dto): Tariff
    {
        $tariff = $this->getRepository()->find($dto->id);
        $courses = Loader::courses()->getMany($dto->relations_ids);
        $this->getService()->detachCourses($tariff, $courses, $dto->user);

        return $tariff->load('courses.instructor');
    }
}