<?php

declare(strict_types=1);

namespace App\Components\Customer;

use App\Common\Contracts;
use App\Common\Contracts\DtoWithUser;
use App\Components\Loader;
use App\Models\Customer;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public const LEGAL_AGE = 18;

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
     * @param Dto $dto
     * @return Model
     * @throws \Throwable
     */
    public function create(DtoWithUser $dto): Model
    {
        $person = Loader::people()->find($dto->person_id);
        return $this->createFromPerson($dto, $person);
    }

    public function update(Model $record, Contracts\DtoWithUser $dto): void
    {
        $person = Loader::people()->find($dto->person_id);
        $this->updateFromPerson($record, $dto, $person);
    }

    /**
     * @param Dto $dto
     * @param Person $person
     * @return Customer
     * @throws Exceptions\IllegalAgeException
     * @throws Exceptions\PersonHasNoPhoneException
     */
    public function createFromPerson(Dto $dto, Person $person): Customer
    {
        $this->validatePersonForBeingCustomer($person, null);

        $dto->name = $dto->name ?? \trans('person.customer_name', $person->compactName());
        $dto->person_id = $person->id;

        return $this->getRepository()->create($dto);
    }

    /**
     * @param Customer $customer
     * @param Dto $dto
     * @param Person $person
     * @return void
     * @throws \Throwable
     * @throws Exceptions\IllegalAgeException
     * @throws Exceptions\PersonHasNoPhoneException
     */
    public function updateFromPerson(Customer $customer, Dto $dto, Person $person): void
    {
        $this->validatePersonForBeingCustomer($person, $customer);

        $dto->name = $dto->name ?? \trans('person.customer_name', $person->compactName());
        $dto->person_id = $person->id;

        parent::update($customer, $dto);
    }

    /**
     * @param Person $person
     * @param Customer|null $customer
     * @return void
     * @throws Exceptions\IllegalAgeException
     * @throws Exceptions\PersonHasNoPhoneException
     */
    private function validatePersonForBeingCustomer(Person $person, ?Customer $customer): void
    {
        if ($person->birth_date?->age < self::LEGAL_AGE) {
            throw new Exceptions\IllegalAgeException();
        }

        if ($person->phone === null) {
            throw new Exceptions\PersonHasNoPhoneException();
        }

        $person->load('customers');
        if (null !== $person->customer && (!$customer || $customer->id !== $person->customer->id)) {
            throw new Exceptions\CustomerAlreadyExists($person->customer);
        }
    }
}