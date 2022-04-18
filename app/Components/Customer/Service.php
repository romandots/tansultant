<?php

declare(strict_types=1);

namespace App\Components\Customer;

use App\Common\BaseService;
use App\Models\Customer;
use App\Models\Person;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            Customer::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Person $person
     * @return Customer
     * @throws \Exception
     */
    public function createFromPerson(Person $person): Customer
    {
        $dto = new Dto();
        $dto->name = $dto->name ?? \trans('person.customer_name', $person->compactName());
        $dto->person_id = $person->id;

        return $this->getRepository()->create($dto);
    }
}