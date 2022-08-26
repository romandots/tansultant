<?php

declare(strict_types=1);

namespace App\Components\Tariff;

use App\Common\BaseComponentFacade;
use App\Models\Course;
use App\Models\Tariff;
use App\Models\User;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Tariff> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Tariff> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Tariff create(Dto $dto, array $relations = [])
 * @method \App\Models\Tariff find(string $id, array $relations = [])
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
     * @param Tariff $tariff
     * @param iterable<Course> $courses
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function findAndAttachCourses(Tariff $tariff, iterable $courses, User $user): void
    {
        $this->getService()->attachCourses($tariff, $courses, $user);
    }

    /**
     * @param Tariff $tariff
     * @param iterable $courses
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function findAndDetachCourses(Tariff $tariff, iterable $courses, User $user): void
    {
        $this->getService()->detachCourses($tariff, $courses, $user);
    }
}