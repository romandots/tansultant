<?php

declare(strict_types=1);

namespace App\Components\User;

use App\Common\Contracts\DtoWithUser;
use App\Components\User\Exceptions\OldPasswordInvalidException;
use App\Events\UserCreatedEvent;
use App\Models\Enum\UserStatus;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    protected \App\Components\Person\Facade $people;
    public function __construct()
    {
        parent::__construct(
            User::class,
            Repository::class,
            Dto::class,
            null
        );
        $this->people = \app(\App\Components\Person\Facade::class);
    }

    /**
     * @param Dto $dto
     * @return User
     * @throws \Throwable
     */
    public function create(DtoWithUser $dto): Model
    {
        // create
        $record = parent::create($dto);
        assert($record instanceof User);

        // fire event
        \event(new UserCreatedEvent($record));
        $this->debug('Fired UserCreatedEvent for user #' . $record->id);

        return $record;
    }

    /**
     * @param Dto $dto
     * @param Person $person
     * @return User
     * @throws \Throwable
     */
    public function createFromPerson(Dto $dto, Person $person): User
    {
        $dto->name = $dto->name ?? \trans('person.user_name', $person->compactName());
        $dto->person_id = $person->id;
        $dto->status = UserStatus::PENDING;

        return $this->create($dto);
    }

    public function updatePassword(User $user, \App\Components\User\UpdateUserPasswordDto $dto): void
    {
        if (!$dto->skip_check && !\Hash::check($dto->old_password, $user->password)) {
            throw new OldPasswordInvalidException();
        }

        $this->getRepository()->updatePassword($user, $dto->new_password);
    }
}