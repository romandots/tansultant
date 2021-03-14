<?php
/**
 * File: PersonRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\DTO\Contracts\FilteredInterface;
use App\Http\Requests\DTO\Contracts\PaginatedInterface;
use App\Http\Requests\DTO\StorePerson as PersonDto;
use App\Http\Requests\ManagerApi\DTO\SearchPeopleDto;
use App\Http\Requests\ManagerApi\DTO\SearchPeopleFilterDto;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * Class PersonRepository
 * @package App\Repository
 */
class PersonRepository extends Repository
{
    public const WITH_SOFT_DELETES = true;
    public const SEARCHABLE_ATTRIBUTES = [
        'last_name',
        'first_name',
        'patronymic_name',
        'phone',
        'email',
        'note',
    ];

    protected function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Person::query();
    }

    /**
     * @param FilteredInterface|SearchPeopleFilterDto $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getFilterQuery(FilteredInterface $filter): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getFilterQuery($filter);

        if ($filter->gender) {
            $query->where('gender', '=', $filter->gender);
        }

        if ($filter->birth_date_from) {
            $query->where('birth_date', '>=', $filter->birth_date_from);
        }

        if ($filter->birth_date_to) {
            $query->where('birth_date', '<=', $filter->birth_date_to);
        }

        return $query;
    }

    /**
     * @param string $id
     * @return Model|Person
     */
    public function find(string $id): Model
    {
        return parent::find($id);
    }

    /**
     * @param string $id
     * @return Model|Person
     */
    public function findTrashed(string $id): Model
    {
        return parent::findTrashed($id);
    }

    /**
     * @param PaginatedInterface|SearchPeopleDto $search
     * @return Person[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findFilteredPaginated(PaginatedInterface $search): \Illuminate\Database\Eloquent\Collection
    {
        return parent::findFilteredPaginated($search, ['instructor', 'customer', 'student', 'user']);
    }

    /**
     * @param string $phoneNumber
     * @return Person|null
     */
    public function getByPhoneNumber(string $phoneNumber): ?Person
    {
        $phoneNumber = \normalize_phone_number($phoneNumber);

        return Person::query()
            ->where('phone', $phoneNumber)
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * @param string $lastName
     * @param string $firstName
     * @param string $patronymicName
     * @param string $gender
     * @param Carbon $birthDate
     * @return Person|null
     */
    public function getByNameGenderAndBirthDate(
        string $lastName,
        string $firstName,
        string $patronymicName,
        string $gender,
        Carbon $birthDate
    ): ?Person {
        return Person::query()
            ->where('last_name', $lastName)
            ->where('first_name', $firstName)
            ->where('patronymic_name', $patronymicName)
            ->where('gender', $gender)
            ->where('birth_date', $birthDate)
            ->whereNull('deleted_at')
            ->first();
    }

    public function create(array $data): Person
    {
        $person = new Person();
        $person->id = \uuid();
        $person->created_at = Carbon::now();
        $person->updated_at = Carbon::now();
        $person->last_name = $data['last_name'] ?? null;
        $person->first_name = $data['first_name'] ?? null;
        $person->patronymic_name = $data['patronymic_name'] ?? null;
        $person->gender = $data['gender'] ?? null;
        $person->birth_date = isset($data['birth_date']) ? Carbon::parse($data['birth_date']) : null;
        $person->phone = $data['phone'] ?? null;
        $person->save();

        return $person;
    }

    /**
     * @param PersonDto $dto
     * @return Person
     * @throws \Exception
     */
    public function createFromDto(PersonDto $dto): Person
    {
        $person = new Person();
        $person->id = \uuid();
        $person->created_at = Carbon::now();
        $person->updated_at = Carbon::now();

        $this->fill($person, $dto);
        $person->save();

        return $person;
    }

    /**
     * @param Person $person
     * @param PersonDto $dto
     */
    public function update(Person $person, PersonDto $dto): void
    {
        $person->updated_at = Carbon::now();

        $this->fill($person, $dto);
        $person->save();
    }

    /**
     * @param Person $person
     * @param PersonDto $dto
     */
    private function fill(Person $person, PersonDto $dto): void
    {
        $person->last_name = $dto->last_name;
        $person->first_name = $dto->first_name;
        $person->patronymic_name = $dto->patronymic_name;
        $person->birth_date = $dto->birth_date;
        $person->gender = $dto->gender;
        $person->phone = $dto->phone;
        $person->email = $dto->email;
        $person->instagram_username = $dto->instagram_username;
        $person->telegram_username = $dto->telegram_username;
        $person->vk_url = $dto->vk_url;
        $person->facebook_url = $dto->facebook_url;
        $person->note = $dto->note;

        if (null !== $dto->picture) {
            $this->savePicture($person, $dto->picture);
        }
    }

    /**
     * @param Person $person
     * @throws \Exception
     */
    public function delete(Person $person): void
    {
        $person->deleted_at = Carbon::now();
        $person->updated_at = Carbon::now();
        $person->save();
    }

    /**
     * @param Person $person
     * @throws \Exception
     */
    public function restore(Person $person): void
    {
        $person->deleted_at = null;
        $person->updated_at = Carbon::now();
        $person->save();
    }

    /**
     * @param Person $person
     * @param \Illuminate\Http\UploadedFile $file
     */
    public function savePicture(Person $person, \Illuminate\Http\UploadedFile $file): void
    {
        $name = Uuid::fromInteger($person->id)->toString();
        $path = $this->getPicturePath($name);
        $person->picture = $file->storePubliclyAs($path, $name);
    }

    /**
     * @param string $name
     * @return string
     */
    private function getPicturePath(string $name): string
    {
        $path = \config('uploads.paths.user_pictures', 'uploads/userpics');

        return "{$path}/{$name[0]}/{$name}";
    }
}
