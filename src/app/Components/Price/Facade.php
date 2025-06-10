<?php

declare(strict_types=1);

namespace App\Components\Price;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Lesson;
use App\Models\Price;
use App\Models\Student;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Price> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Price> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Price create(Dto $dto, array $relations = [])
 * @method \App\Models\Price find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Price findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Price findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function findByName(string|int $name): Price
    {
        return $this->getRepository()->findBy('name', (string)$name);
    }

    public function findByPriceValue(int $price): Price
    {
        return $this->getRepository()->findBy('price', $price);
    }

    public function calculateLessonVisitPrice(Lesson $lesson, Student $student): float
    {
        return $this->getService()->calculateLessonVisitPrice($lesson, $student);
    }
}