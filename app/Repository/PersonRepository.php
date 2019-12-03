<?php
/**
 * File: PersonRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-17
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Http\Requests\ManagerApi\DTO\StorePerson as PersonDto;
use App\Models\Person;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

/**
 * Class PersonRepository
 * @package App\Repository
 */
class PersonRepository
{
    /**
     * @param string $id
     * @return Person|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find(string $id): ?Person
    {
        return Person::query()->findOrFail($id);
    }

    /**
     * @param PersonDto $dto
     * @return Person
     * @throws \Exception
     */
    public function create(PersonDto $dto): Person
    {
        $person = new Person;
        $person->id = \uuid();
        $this->fill($person, $dto);
        $person->save();

        return $person;
    }

    /**
     * @param Person $person
     * @param PersonDto $dto
     * @return Person
     */
    public function update(Person $person, PersonDto $dto): Person
    {
        $this->fill($person, $dto);
        $person->save();

        return $person;
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
        $person->created_at = Carbon::now();

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
        $person->delete();
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
