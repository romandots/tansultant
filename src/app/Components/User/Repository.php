<?php

declare(strict_types=1);

namespace App\Components\User;

use App\Models\Enum\UserStatus;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method User make()
 * @method int countFiltered(\App\Common\Contracts\SearchFilterDto $search)
 * @method \Illuminate\Database\Eloquent\Collection<User> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method User find(string $id)
 * @method User findTrashed(string $id)
 * @method User create(Dto $dto)
 * @method void update($record, Dto $dto)
 * @method void delete(User $record)
 * @method void restore(User $record)
 * @method void forceDelete(User $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            User::class,
            ['name']
        );
    }

    public function findByUsername(string $username): User
    {
        return $this->getQuery()->where('username', $username)->firstOrFail();
    }

    /**
     * @param User $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->status = $dto->status;
        if(isset($dto->person_id)) {
            $record->person_id = $dto->person_id;
        }
        $record->name = $dto->name;
        $record->username = $dto->username;
        if ($dto->password) {
            $record->password = \Hash::make($dto->password);
        }
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function updatePassword(User $user, string $password): void
    {
        $user->password = \Hash::make($password);
        $user->updated_at = Carbon::now();
        $user->save();
    }

    /**
     * @param User $user
     */
    public function updateSeenAt(User $user): void
    {
        $user->seen_at = Carbon::now();
        $user->save();
    }

    public function setApproved(User $user): void
    {
        $this->setStatus($user, UserStatus::APPROVED, ['approved_at']);
        $this->save($user);
    }

    public function setDisabled(User $user): void
    {
        $this->setStatus($user, UserStatus::DISABLED);
        $this->save($user);
    }

    public function attachActiveShift(User $user, Shift $shift): void
    {
        $user->active_shift_id = $shift->id;
        $this->save($user);
    }

    public function detachActiveShift(User $user): void
    {
        $user->active_shift_id = null;
        $this->save($user);
    }
}