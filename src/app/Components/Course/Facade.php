<?php

declare(strict_types=1);

namespace App\Components\Course;

use App\Common\BaseComponentFacade;
use App\Common\DTO\IdsDto;
use App\Common\DTO\ShowDto;
use App\Components\Loader;
use App\Models\Course;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Course> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Course> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Course create(Dto $dto, array $relations = [])
 * @method \App\Models\Course find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Course findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Course findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    /**
     * @param string $id
     * @param \App\Models\User $user
     * @throws \Exception
     */
    public function disable(string $id, \App\Models\User $user): void
    {
        $course = $this->getRepository()->find($id);
        $this->getService()->disable($course, $user);
    }

    /**
     * @param string $id
     * @param \App\Models\User $user
     * @throws \Exception
     */
    public function enable(string $id, \App\Models\User $user): void
    {
        $course = $this->getRepository()->find($id);
        $this->getService()->disable($course, $user);
    }

    /**
     * @param IdsDto $dto
     * @return Course
     * @throws \Exception
     */
    public function findAndAttachTariffs(IdsDto $dto): Course
    {
        $course = $this->getRepository()->find($dto->id);
        $tariffs = Loader::tariffs()->getMany($dto->relations_ids);
        $this->getService()->attachTariffs($course, $tariffs, $dto->user);

        return $course
            ->load($dto->with)
            ->loadCount($dto->with_count);
    }

    /**
     * @param IdsDto $dto
     * @return Course
     * @throws \Exception
     */
    public function findAndDetachTariffs(IdsDto $dto): Course
    {
        $course = $this->getRepository()->find($dto->id);
        $tariffs = Loader::tariffs()->getMany($dto->relations_ids);
        $this->getService()->detachTariffs($course, $tariffs, $dto->user);

        return $course
            ->load($dto->with)
            ->loadCount($dto->with_count);
    }
}