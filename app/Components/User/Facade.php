<?php

declare(strict_types=1);

namespace App\Components\User;

use App\Common\BaseComponentFacade;
use App\Common\DTO\ShowDto;
use App\Components\Loader;
use App\Models\User;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(\App\Common\DTO\SuggestDto $suggestDto, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\User> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\User> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\DTO\SearchDto $searchParams)
 * @method \App\Models\User create(Dto $dto, array $relations = [])
 * @method \App\Models\User find(ShowDto $showDto)
 * @method void findAndDelete(string $id)
 * @method \App\Models\User findAndRestore(string $id, array $relations = [])
 * @method \App\Models\User findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function updatePassword(User $user, UpdateUserPasswordDto $updateUserPasswordDto): void
    {
        $this->getService()->updatePassword($user, $updateUserPasswordDto);
    }

    public function updateSeenAt(User $user): void
    {
        $this->getRepository()->updateSeenAt($user);
    }

    public function createWithoutPerson(Dto $dto): User
    {
        return $this->getService()->create($dto);
    }

    public function createFromPerson(Dto $dto): User
    {
        $person = Loader::people()->findById($dto->person_id);
        return $this->getService()->createFromPerson($dto, $person);
    }

    public function updateFromPerson(User $record, Dto $dto): User
    {
        $person = Loader::people()->findById($dto->person_id);
        $this->getService()->updateFromPerson($record, $dto, $person);
        return $record->refresh();
    }

    public function findByUsername(string $username): User
    {
        return $this->getRepository()->findByUsername($username);
    }

    public function approve(User $user): void
    {
        $this->getService()->approve($user);
    }

    public function disable(User $user): void
    {
        $this->getService()->disable($user);
    }
}