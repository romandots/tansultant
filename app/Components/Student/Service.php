<?php

declare(strict_types=1);

namespace App\Components\Student;

use App\Common\Contracts;
use App\Components\Customer\Exceptions\CustomerAlreadyExists;
use App\Components\Loader;
use App\Models\Customer;
use App\Models\Enum\StudentStatus;
use App\Models\Person;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Student::class,
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
    public function create(Contracts\DtoWithUser $dto): Model
    {
        $person = Loader::people()->find($dto->person_id);
        return $this->createFromPerson($dto, $person);
    }

    /**
     * @param Model $record
     * @param Dto $dto
     * @return void
     * @throws \Throwable
     */
    public function update(Model $record, Contracts\DtoWithUser $dto): void
    {
        $person = Loader::people()->find($dto->person_id);
        $this->updateFromPerson($record, $dto, $person);
    }


    /**
     * @param Dto $dto
     * @param Person $person
     * @return Student
     */
    public function createFromPerson(Dto $dto, Person $person): Student
    {
        $this->validatePerson($person, null);

        $customer = $dto->student_is_customer
            ? $this->findOrCreateNewCustomerFromPerson($person, $dto->getUser())
            : Loader::customers()->find($dto->customer_id);

        $dto->name = $this->getNameFromPerson($dto, $person);
        $dto->person_id = $person->id;
        $dto->customer_id = $customer?->id;
        $dto->status = StudentStatus::POTENTIAL;

        return parent::create($dto);
    }

    /**
     * @param Student $record
     * @param Dto $dto
     * @param Person $person
     */
    public function updateFromPerson(Student $record, Dto $dto, Person $person): void
    {
        $this->validatePerson($person, $record);

        Loader::customers()->find($dto->customer_id);

        $dto->name = $this->getNameFromPerson($dto, $person);
        $dto->person_id = $person->id;
        $dto->status = $record->status;

        parent::update($record, $dto);
    }

    private function findOrCreateNewCustomerFromPerson(Person $person, User $user): Customer
    {
        try {
            return Loader::customers()->createFromPerson(new \App\Components\Customer\Dto($user), $person);
        } catch (CustomerAlreadyExists $alreadyExistsException) {
            return $alreadyExistsException->getCustomer();
        }
    }

    /**
     * @param Dto $dto
     * @param Person $person
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null
     */
    private function getNameFromPerson(
        Dto $dto,
        Person $person
    ): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|array|null {
        return $dto->name ?? \trans('person.student_name', $person->compactName());
    }

    private function validatePerson(Person $person, ?Student $student): void
    {
        $person->load('students');
        if ($person->student && $person->student->id === $student?->id) {
            throw new Exceptions\StudentAlreadyExists($person->student);
        }
    }

    public function activatePotentialStudent(Student $student, User $user): void
    {
        if ($student->status === StudentStatus::POTENTIAL && $student->loadCount('visits')->visits_count > 0) {
            $this->debug("Activating student {$student->name}");
            $this->getRepository()->updateStatus($student, StudentStatus::ACTIVE);
            $this->history->logActivate($user, $student);
        }
    }

    public function dectivateStudent(Student $student, User $user): void
    {
        if ($student->status !== StudentStatus::POTENTIAL && $student->loadCount('visits')->visits_count === 0) {
            $this->debug("Deactivating student {$student->name}");
            $this->getRepository()->updateStatus($student, StudentStatus::POTENTIAL);
            $this->history->logDeactivate($user, $student);
        }
    }

    public function updateLastSeen(Student $student, ?Carbon $date = null): void
    {
        $this->getRepository()->updateLastSeenTimestamp($student->id, $date);
    }
}