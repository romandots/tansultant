<?php

declare(strict_types=1);

namespace App\Components\Schedule;

use App\Common\Contracts;
use App\Models\Enum\ScheduleCycle;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Schedule::class,
            Repository::class,
            Dto::class,
            null
        );
    }

    /**
     * @param Dto $dto
     * @return Schedule
     * @throws \Throwable
     */
    public function create(Contracts\DtoWithUser $dto): Model
    {
        if ($dto->cycle === ScheduleCycle::EVERY_WEEK) {
            foreach ($dto->weekdays as $weekday) {
                $dto->weekday = $weekday;
                $lastOne = parent::create($dto);
            }
        }

        if (!isset($lastOne)) {
            throw new \Exception('Schedule was not created. Check arguments.');
        }

        return $lastOne;
    }

    /**
     * @param Schedule $record
     * @param Dto $dto
     * @throws \Throwable
     */
    public function update(Model $record, Contracts\DtoWithUser $dto): void
    {
        if ($dto->cycle === ScheduleCycle::EVERY_WEEK && !$dto->weekday) {
            throw new \Exception('Schedule was not created. Check arguments.');
        }

        parent::update($record, $dto);
    }


}