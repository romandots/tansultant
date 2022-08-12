<?php

declare(strict_types=1);

namespace App\Components\Person;

use App\Common\Contracts;
use App\Components\Loader;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Person::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Contracts\DtoWithUser $dto
     * @return Model
     * @throws \Throwable
     * @throws Exceptions\PhoneAlreadyRegistered
     * @throws Exceptions\PersonAlreadyRegistered
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        $this->checkIfPersonExists($dto, null);
        return parent::create($dto); //
    }

    /**
     * @param Model $record
     * @param Contracts\DtoWithUser $dto
     * @return void
     * @throws \Throwable
     * @throws Exceptions\PhoneAlreadyRegistered
     * @throws Exceptions\PersonAlreadyRegistered
     */
    public function update(Model $record, Contracts\DtoWithUser $dto): void
    {
        $this->checkIfPersonExists($dto, $record->id);
        parent::update($record, $dto);
    }


    /**
     * @param Dto $dto
     * @param string|null $ignoreId
     * @return void
     * @throws Exceptions\PhoneAlreadyRegistered
     * @throws Exceptions\PersonAlreadyRegistered
     */
    protected function checkIfPersonExists(Dto $dto, ?string $ignoreId): void
    {
        // Check by phone
        $existingRecord = Loader::people()->getByPhoneNumber($dto->phone);
        if ($existingRecord && $ignoreId !== $existingRecord->id) {
            throw new Exceptions\PhoneAlreadyRegistered($dto->phone, $existingRecord);
        }

        // Check by bio
        $existingRecord = Loader::people()->getByNameGenderAndBirthDate(
            $dto->last_name,
            $dto->first_name,
            $dto->patronymic_name,
            $dto->gender,
            $dto->birth_date
        );
        if ($existingRecord && $ignoreId !== $existingRecord->id) {
            throw new Exceptions\PersonAlreadyRegistered($dto, $existingRecord);
        }
    }
}