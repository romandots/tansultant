<?php

declare(strict_types=1);

namespace App\Components\Bonus;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Models\Bonus;
use App\Models\Student;
use Illuminate\Support\Collection;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Bonus> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Bonus> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Bonus create(Dto $dto, array $relations = [])
 * @method \App\Models\Bonus find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\Bonus findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Bonus findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    /**
     * @param Student $student
     * @return Collection<Bonus>
     * @todo implement
     */
    public function getStudentAvailableBonuses(Student $student): Collection
    {
        return new Collection();
    }

    public function activateBonus(Bonus $bonus): void
    {
        $this->getService()->activateBonus($bonus);
    }

    public function cancelBonus(Bonus $bonus): void
    {
        $this->getService()->cancelBonus($bonus);
    }

    public function expireBonus(Bonus $bonus): void
    {
        $this->getService()->expireBonus($bonus);
    }

    public function resetBonus(Bonus $bonus): void
    {
        $this->getService()->resetBonus($bonus);
    }
}