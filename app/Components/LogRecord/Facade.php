<?php

declare(strict_types=1);

namespace App\Components\LogRecord;

use App\Common\BaseFacade;
use App\Common\Locator;
use App\Models\Enum\LogRecordAction;
use App\Models\User;
use Illuminate\Support\Collection;

class Facade extends BaseFacade
{
    public function getService(): Service
    {
        return Locator::get(Service::class);
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

    public function logOpen(User $user, object $object): void
    {
        $this->getService()->log($user, LogRecordAction::OPEN, $object);
    }

    public function logClose(User $user, object $object): void
    {
        $this->getService()->log($user, LogRecordAction::CLOSE, $object);
    }

    public function logBook(User $user, object $object): void
    {
        $this->getService()->log($user, LogRecordAction::BOOK, $object);
    }

    public function logCancel(User $user, object $object): void
    {
        $this->getService()->log($user, LogRecordAction::CANCEL, $object);
    }

    public function logCheckout(User $user, object $object): void
    {
        $this->getService()->log($user, LogRecordAction::CHECKOUT, $object);
    }

    public function getHistory(\App\Models\Enum\LogRecordObjectType $objectType, string $id): Collection
    {
        return $this->getService()->getRepository()->getAllByObjectTypeAndObjectId($objectType, $id);
    }
}