<?php

declare(strict_types=1);

namespace App\Components\LogRecord;

use App\Common\BaseComponentFacade;
use App\Models\Enum\LogRecordAction;
use App\Models\User;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\LogRecord> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\LogRecord> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\LogRecord create(Dto $dto, array $relations = [])
 * @method \App\Models\LogRecord find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\LogRecord findAndRestore(string $id, array $relations = [])
 * @method \App\Models\LogRecord findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $object
     * @throws \Exception
     */
    public function logCreate(User $user, object $object): void
    {
        $this->getService()->log($user, LogRecordAction::CREATE, $object);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $updatedObject
     * @param object $oldObject
     * @throws \Exception
     */
    public function logUpdate(User $user, object $updatedObject, object $oldObject): void
    {
        $this->getService()->log($user, LogRecordAction::UPDATE, $updatedObject, $oldObject);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $object
     * @throws \Exception
     */
    public function logDelete(User $user, object $object): void
    {
        $this->getService()->log($user, LogRecordAction::DELETE, \get_class($object), $object);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $restoredObject
     * @throws \Exception
     */
    public function logRestore(User $user, object $restoredObject): void
    {
        $this->getService()->log($user, LogRecordAction::RESTORE, $restoredObject);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $object
     * @param object|null $oldValue
     * @throws \Exception
     */
    public function logEnable(User $user, object $object, object $oldValue): void
    {
        $this->getService()->log($user, LogRecordAction::ENABLE, $object, $oldValue);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $object
     * @param object|null $oldValue
     * @throws \Exception
     */
    public function logDisable(User $user, object $object, object $oldValue): void
    {
        $this->getService()->log($user, LogRecordAction::DISABLE, $object, $oldValue);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $object
     * @throws \Exception
     */
    public function logSend(User $user, object $object): void
    {
        $this->getService()->log($user, LogRecordAction::SEND, $object);
    }
}