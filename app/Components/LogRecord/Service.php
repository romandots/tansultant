<?php

declare(strict_types=1);

namespace App\Components\LogRecord;

use App\Models\Enum\LogRecordAction;
use App\Models\Enum\LogRecordObjectType;
use App\Models\User;

class Service extends \App\Common\BaseService
{
    public function getRepository(): Repository
    {
        return \app(Repository::class);
    }

    /**
     * Universal log method
     * creates log record for action
     *
     * @param User $user
     * @param LogRecordAction $action
     * @param object|string $objectOrClassName
     * @param object|null $oldValue
     * @throws \Exception
     */
    public function log(User $user, LogRecordAction $action, object|string $objectOrClassName, ?object $oldCopy = null): void
    {
        $className = \is_object($objectOrClassName) ? \get_class($objectOrClassName) : $objectOrClassName;
        $objectType = LogRecordObjectType::getFromClass($className);
        $messageKey = "log_record.{$objectType->value}.{$action->value}";

        $dto = new Dto($user);
        $dto->action = $action;
        $dto->message = \trans($messageKey, [
            'user' => $user->name,
            'object' => \is_object($objectOrClassName) ? $objectOrClassName->name : null,
        ]);
        $dto->object_type = LogRecordObjectType::getFromClass($className);
        $dto->object_id = is_object($objectOrClassName) ? (string)$objectOrClassName->id : $oldCopy?->id;

        $this->getRepository()->create($dto);
    }
}