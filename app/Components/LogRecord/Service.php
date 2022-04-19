<?php

declare(strict_types=1);

namespace App\Components\LogRecord;

use App\Models\Enum\LogRecordAction;
use App\Models\LogRecord;
use App\Models\User;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            LogRecord::class,
            Repository::class,
            Dto::class,
            null
        );
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
    public function log(User $user, LogRecordAction $action, object|string $objectOrClassName, ?object $oldValue = null): void
    {
        $className = \is_object($objectOrClassName) ? \get_class($objectOrClassName) : $objectOrClassName;
        $messageKey = "log_record.{$className}.{$action->value}";

        $dto = new Dto();
        $dto->action = $action;
        $dto->message = \trans($messageKey, [
            'user' => $user->name,
            'object' => \is_object($objectOrClassName) ? $objectOrClassName->name : null,
        ]);;

        $this->getRepository()->create($dto);
    }
}