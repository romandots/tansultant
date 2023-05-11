<?php

declare(strict_types=1);

namespace App\Components\Schedule;

use App\Common\Contracts;
use App\Components\Loader;
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
                $lastOne = $this->_createOne($dto);
            }
        } else {
            $lastOne = $this->_createOne($dto);
        }

        if (!isset($lastOne)) {
            throw new \Exception('Schedule was not created. Check arguments.');
        }

        return $lastOne;
    }

    protected function _createOne($dto): Schedule
    {
        /** @var Schedule $record */
        $record = parent::create($dto);

        // Regenerate lessons for starting date of newly created schedule
        $this->generateLessons($record->from_date);

        return $record;
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

        // Regenerate lessons for starting date of updated schedule
        $this->generateLessons($record->refresh()->from_date);

        parent::update($record, $dto);
    }

    protected function generateLessons(\Illuminate\Support\Carbon $date): void
    {
        Loader::lessons()->generateLessonsOnDate($date);
    }
}