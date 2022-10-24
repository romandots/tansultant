<?php

declare(strict_types=1);

namespace App\Components\Course;

use App\Common\Contracts;
use App\Events\Course\CourseEvent;
use App\Models\Course;
use App\Models\Enum\CourseStatus;
use App\Models\Enum\TariffStatus;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends \App\Common\BaseComponentService
{
    public function __construct()
    {
        parent::__construct(
            Course::class,
            Repository::class,
            Dto::class,
        );
    }

    public function create(Contracts\DtoWithUser $dto): Model
    {
        // create
        $record = parent::create($dto);
        assert($dto instanceof Dto);
        assert($record instanceof Course);

        // sync genre tags
        $record->syncTagsWithType($dto->genres, Genre::class);
        $this->debug('Assigned genres with course #' . $record->id, $dto->genres);

        // fire event
        //\event(new CourseCreatedEvent($record, $dto->getUser()));
        CourseEvent::created($record, $dto->getUser());
        $this->debug('Fired CourseCreatedEvent for course #' . $record->id);

        return $record;
    }

    public function update(Model $record, Contracts\DtoWithUser $dto): void
    {
        assert($dto instanceof Dto);
        assert($record instanceof Course);

        parent::update($record, $dto);

        // sync genre tags
        $record->syncTagsWithType($dto->genres, Genre::class);
        $this->debug('Assigned genres with course #' . $record->id, $dto->genres);

        // fire event
        //\event(new CourseUpdatedEvent($record, $dto->getUser()));
        CourseEvent::updated($record, $dto->getUser());
        $this->debug('Fired CourseUpdatedEvent for course #' . $record->id);
    }

    /**
     * Set course status to pending or active (according to working dates)
     *
     * @param Course $course
     * @param User $user
     * @throws \Exception
     */
    public function enable(Course $course, User $user): void
    {
        $oldCourse = clone $course;

        // switch status according to date
        if ($course->isInPeriod()) {
            $this->getRepository()->setActive($course);
        } else {
            $this->getRepository()->setPending($course);
        }

        // log event
        $this->history->logEnable($user, $course, $oldCourse);

        // fire event
        //\event(new CourseEnabledEvent($course, $user));
        CourseEvent::enabled($course, $user);
        $this->debug('Fired CourseEnabledEvent for course #' . $course->id);
    }

    /**
     * Set course status to disabled
     *
     * @param Course $course
     * @param User $user
     * @throws \Exception
     */
    public function disable(Course $course, User $user): void
    {
        $oldCourse = clone $course;

        // disable
        $this->debug('Disabling course #' . $course->id);
        $this->getRepository()->setDisabled($course);

        // log event
        $this->history->logDisable($user, $course, $oldCourse);

        // fire event
        //\event(new CourseDisabledEvent($course, $user));
        CourseEvent::disabled($course, $user);
        $this->debug('Fired CourseDisabledEvent for course #' . $course->id);
    }

    /**
     * @param Model $record
     * @param User $user
     * @throws \Throwable
     */
    public function delete(Model $record, User $user): void
    {
        // delete
        parent::delete($record, $user);

        // fire event
        //\event(new CourseDeletedEvent($record, $user));
        CourseEvent::deleted($record, $user);
        $this->debug('Fired CourseDeletedEvent for course #' . $record->id);
    }

    public function restore(Model $record, User $user): void
    {
        // restore
        parent::restore($record, $user);

        // fire event
        CourseEvent::restored($record, $user);
        $this->debug('Fired CourseRestoredEvent for course #' . $record->id);
    }

    /**
     * @param Course $course
     * @param iterable $tariffs
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function attachTariffs(Course $course, iterable $tariffs, User $user): void
    {
        if ($course->status === CourseStatus::DISABLED) {
            throw new Exceptions\CannotAttachDisabledCourse($course);
        }

        foreach ($tariffs as $tariff) {
            if ($tariff->status === TariffStatus::ARCHIVED) {
                throw new Exceptions\CannotAttachArchivedTariff($tariff);
            }
        }

        $originalRecord = clone $course;
        $this->getRepository()->attachTariffs($course, $tariffs);
        $this->debug("Attach tariffs to course {$course->name}", (array)$tariffs);
        $this->history->logUpdate($user, $course, $originalRecord);
    }

    /**
     * @param Course $course
     * @param iterable<Course> $tariffs
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function detachTariffs(Course $course, iterable $tariffs, User $user): void
    {
        $originalRecord = clone $course;
        $this->getRepository()->detachTariffs($course, $tariffs);
        $this->debug("Detach tariffs from course {$course->name}", (array)$tariffs);
        $this->history->logUpdate($user, $course, $originalRecord);
    }
}