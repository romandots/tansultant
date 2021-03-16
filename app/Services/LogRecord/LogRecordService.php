<?php
/**
 * File: LogRecordService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-10
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Services\LogRecord;

use App\Models\LogRecord;
use App\Models\User;
use App\Repository\LogRecordRepository;

/**
 * Class LogRecordService
 * @package App\Services\LogRecord
 */
class LogRecordService
{
    private LogRecordRepository $repository;

    /**
     * LogRecordService constructor.
     * @param LogRecordRepository $repository
     */
    public function __construct(LogRecordRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Universal log method
     * creates log record for action
     *
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param string $action
     * @param object|string $objectOrClassName
     * @param object|null $oldValue
     * @throws \Exception
     */
    private function log(User $user, string $action, $objectOrClassName, ?object $oldValue = null): void
    {
        $className = \is_object($objectOrClassName) ? \get_class($objectOrClassName) : $objectOrClassName;
        $messageKey = "log_record.{$className}.{$action}";
        $message = \trans($messageKey, [
            'user' => $user->name,
            'object' => \is_object($objectOrClassName) ? $objectOrClassName->name : null,
        ]);
        $this->repository->create($user, $action, $message, $objectOrClassName, $oldValue);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $object
     * @throws \Exception
     */
    public function logCreate(User $user, object $object): void
    {
        $this->log($user, LogRecord::ACTION_CREATE, $object);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $updatedObject
     * @param object $oldObject
     * @throws \Exception
     */
    public function logUpdate(User $user, object $updatedObject, object $oldObject): void
    {
        $this->log($user, LogRecord::ACTION_UPDATE, $updatedObject, $oldObject);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $object
     * @throws \Exception
     */
    public function logDelete(User $user, object $object): void
    {
        $this->log($user, LogRecord::ACTION_DELETE, \get_class($object), $object);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $restoredObject
     * @throws \Exception
     */
    public function logRestore(User $user, $restoredObject): void
    {
        $this->log($user, LogRecord::ACTION_RESTORE, $restoredObject);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $object
     * @param object|null $oldValue
     * @throws \Exception
     */
    public function logEnable(User $user, object $object, object $oldValue): void
    {
        $this->log($user, LogRecord::ACTION_ENABLE, $object, $oldValue);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $object
     * @param object|null $oldValue
     * @throws \Exception
     */
    public function logDisable(User $user, object $object, object $oldValue): void
    {
        $this->log($user, LogRecord::ACTION_DISABLE, $object, $oldValue);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User|User $user
     * @param object $object
     * @throws \Exception
     */
    public function logSend(User $user, object $object): void
    {
        $this->log($user, LogRecord::ACTION_SEND, $object);
    }
}
