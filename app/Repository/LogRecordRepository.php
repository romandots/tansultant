<?php
/**
 * File: LogRecordRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-10
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\LogRecord;
use App\Models\User;
use Carbon\Carbon;

class LogRecordRepository
{
    /**
     * @param User $user
     * @param string $action
     * @param string $message
     * @param object|string $newValue
     * @param object|null $oldValue
     * @return LogRecord
     * @throws \Exception
     */
    public function create(User $user, string $action, string $message, $newValue, ?object $oldValue = null): LogRecord
    {
        if (\is_string($newValue) && null === $oldValue) {
            throw new \LogicException('LogRecord with empty object called');
        }

        $className = \is_object($newValue) ? \get_class($newValue) : $newValue;

        $logRecord = new LogRecord();
        $logRecord->id = \uuid();
        $logRecord->object_type = $className;
        $logRecord->object_id = \is_object($newValue) ? $newValue->id : $oldValue->id;
        $logRecord->user_id = $user->id;
        $logRecord->action = $action;
        $logRecord->message = $message;
        $logRecord->old_value = $oldValue;
        $logRecord->new_value = \is_object($newValue) ? $newValue : null;
        $logRecord->created_at = Carbon::now();

        $logRecord->save();

        return $logRecord;
    }
}
