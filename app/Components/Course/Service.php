<?php

declare(strict_types=1);

namespace App\Components\Course;

use App\Common\BaseService;
use App\Common\Contracts;
use App\Events\Course\CourseCreatedEvent;
use App\Events\Course\CourseDeletedEvent;
use App\Events\Course\CourseDisabledEvent;
use App\Events\Course\CourseEnabledEvent;
use App\Events\Course\CourseUpdatedEvent;
use App\Models\Course;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            Course::class,
            Repository::class,
            Dto::class,
            null
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
        \event(new CourseCreatedEvent($record, $dto->getUser()));
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
        \event(new CourseUpdatedEvent($record, $dto->getUser()));
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
        $this->actions->logEnable($user, $course, $oldCourse);

        // fire event
        \event(new CourseEnabledEvent($course, $user));
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
        $this->actions->logDisable($user, $course, $oldCourse);

        // fire event
        \event(new CourseDisabledEvent($course, $user));
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
        \event(new CourseDeletedEvent($record, $user));
        $this->debug('Fired CourseDeletedEvent for course #' . $record->id);
    }
}