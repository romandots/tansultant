<?php

declare(strict_types=1);

namespace App\Components\Payment;

use App\Common\BaseComponentFacade;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use App\Models\Visit;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\Payment> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\Payment> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\Payment create(Dto $dto, array $relations = [])
 * @method \App\Models\Payment find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\Payment findAndRestore(string $id, array $relations = [])
 * @method \App\Models\Payment findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    /**
     * @throws \Exception
     */
    public function createVisitPayment(int $price, Visit $visit, Student $student, ?User $user = null): Payment
    {
        return $this->getService()->createVisitPayment($price, $visit, $student, $user);
    }
}