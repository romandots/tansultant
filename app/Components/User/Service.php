<?php

declare(strict_types=1);

namespace App\Components\User;

use App\Common\Contracts\DtoWithUser;
use App\Components\Customer\Exceptions\PersonHasNoPhoneException;
use App\Components\User\Exceptions\OldPasswordInvalidException;
use App\Events\User\UserCreatedEvent;
use App\Models\Enum\UserStatus;
use App\Models\Person;
use App\Models\User;
use App\Notifications\TextMessages\PasswordResetSmsNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();

        // create
        $record = parent::create($dto);
        assert($record instanceof User);

        // assign role
        $this->debug('Assigning roles to user #' . $record->id);
        $record->assignRole($dto->roles);

        DB::commit();

        // fire event
        \event(new UserCreatedEvent($record));
        $this->debug('Fired UserCreatedEvent for user #' . $record->id);

        // Send password
        $this->sendUserHisPassword($record, $dto->password);

        return $record;
    }

    /**
     * @param User $record
     * @param Dto $dto
     * @return void
     * @throws \Throwable
     */
    public function update(Model $record, DtoWithUser $dto): void
    {
        DB::beginTransaction();
        parent::update($record, $dto);

        // assign role
        $this->debug('Assigning roles to user #' . $record->id);
        $record->syncRoles($dto->roles);
        DB::commit();
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
        $dto->username = $dto->username ?? $person->email ?? $person->phone;
        if (null === $dto->username) {
            throw new PersonHasNoPhoneException();
        }

        $dto->person_id = $person->id;
        $dto->status = UserStatus::APPROVED;
        $dto->password = \Str::random(8);

        return $this->create($dto);
    }

    /**
     * @param User $record
     * @param Dto $dto
     * @param Person $person
     * @return void
     * @throws \Throwable
     */
    public function updateFromPerson(User $record, Dto $dto, Person $person): void
    {
        $dto->name = $dto->name ?? \trans('person.user_name', $person->compactName());
        $dto->username = $dto->username ?? $person->email ?? $person->phone;
        if (null === $dto->username) {
            throw new PersonHasNoPhoneException();
        }

        $dto->person_id = $person->id;
        $dto->status = UserStatus::APPROVED;

        $this->update($record, $dto);
    }

    public function updatePassword(User $user, \App\Components\User\UpdateUserPasswordDto $dto): void
    {
        if (!$dto->skip_check && !\Hash::check($dto->old_password, $user->password)) {
            throw new OldPasswordInvalidException();
        }

        $this->getRepository()->updatePassword($user, $dto->new_password);
    }

    public function approve(User $user): void
    {
        $this->debug('Approving user #' . $user->id . ': setting status to `approved`');
        $this->getRepository()->setApproved($user);
    }

    public function disable(User $user): void
    {
        $this->debug('Disabling user #' . $user->id . ': setting status to `disabled`');
        $this->getRepository()->setDisabled($user);
    }

    protected function sendUserHisPassword(User $user, string $password): void
    {
        $this->debug('Sending password to user #' . $user->id);
        try {
            $user->notify(new PasswordResetSmsNotification($password));
        } catch (\Throwable $exception) {
            $this->error('Failed to send password to user #' . $user->id . ': ' . $exception->getMessage(), [
                'user' => $user->toArray(),
                'password' => $password,
            ]);
            throw $exception;
        }
    }
}