<?php

declare(strict_types=1);

namespace App\Components\Person;

use App\Common\DTO\SearchFilterDto;
use App\Http\Requests\ManagerApi\DTO\SearchPeopleFilterDto;
use App\Models\Enum\Gender;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method Person make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<Person> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method Person find(string $id)
 * @method Person findTrashed(string $id)
 * @method Person create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(Person $record)
 * @method void restore(Person $record)
 * @method void forceDelete(Person $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            modelClass: Person::class,
            searchableAttributes: [
                'last_name',
                'first_name',
                'patronymic_name',
                'phone',
                'email',
                'note',
            ]
        );
    }

    public function getFilterQuery(
        SearchFilterDto $filter,
        array $relations = [],
        array $countRelations = []
    ): \Illuminate\Database\Eloquent\Builder {
        assert($filter instanceof SearchPeopleFilterDto);
        $query = parent::getFilterQuery($filter, $relations, $countRelations);

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
     * @param Person $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->last_name = $dto->last_name;
        $record->first_name = $dto->first_name;
        $record->patronymic_name = $dto->patronymic_name;
        $record->birth_date = isset($dto->birth_date) ? $dto->birth_date : null;
        $record->gender = $dto->gender;
        $record->phone = $dto->phone ? \normalize_phone_number($dto->phone) : null;
        $record->email = $dto->email;
        $record->instagram_username = $dto->instagram_username;
        $record->telegram_username = $dto->telegram_username;
        $record->note = $dto->note;

        if (null !== $dto->picture) {
            $this->savePicture($record, $dto->picture);
        }
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

    /**
     * @param string $phoneNumber
     * @return Person|null
     */
    public function getByPhoneNumber(string $phoneNumber): ?Person
    {
        $phoneNumber = \normalize_phone_number($phoneNumber);

        return $this->getQuery()
            ->where('phone', $phoneNumber)
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * @param string $lastName
     * @param string $firstName
     * @param string $patronymicName
     * @param Gender $gender
     * @param Carbon $birthDate
     * @return Person|null
     */
    public function getByNameGenderAndBirthDate(
        string $lastName,
        string $firstName,
        string $patronymicName,
        Gender $gender,
        Carbon $birthDate
    ): ?Person {
        return $this->getQuery()
            ->where('last_name', $lastName)
            ->where('first_name', $firstName)
            ->where('patronymic_name', $patronymicName)
            ->where('gender', $gender->value)
            ->where('birth_date', $birthDate)
            ->whereNull('deleted_at')
            ->first();
    }
}