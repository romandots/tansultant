<?php

declare(strict_types=1);

namespace App\Components\Lesson;

use App\Common\BaseService;
use App\Common\Contracts\DtoWithUser;
use App\Http\Requests\ManagerApi\DTO\LessonsFiltered;
use App\Http\Requests\ManagerApi\DTO\StoreLesson as LessonDto;
use App\Http\Requests\PublicApi\DTO\LessonsOnDate;
use App\Jobs\GenerateLessonsOnDateJob;
use App\Models\Course;
use App\Models\Enum\LessonType;
use App\Models\Lesson;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @method Repository getRepository()
 */
class Service extends BaseService
{
    protected \App\Components\Course\Facade $courses;
    protected \App\Components\Classroom\Facade $classrooms;

    public function __construct()
    {
        parent::__construct(
            Lesson::class,
            Repository::class,
            Dto::class,
            null
        );
        $this->courses = \app(\App\Components\Course\Facade::class);
        $this->classrooms = \app(\App\Components\Classroom\Facade::class);
    }

    public function createFromScheduleOnDate(Schedule $schedule, Carbon $date): Lesson
    {
        $startTime = $this->getDateAndTime($date, Carbon::parse($schedule->starts_at));
        $endTime =  $this->getDateAndTime($date, Carbon::parse($schedule->ends_at));

        $dto = new Dto();
        $dto->schedule_id = $schedule->id;
        $dto->instructor_id = $schedule->course->instructor_id;
        $dto->course_id = $schedule->course_id;
        $dto->classroom_id = $schedule->classroom_id;
        $dto->branch_id = $schedule->branch_id;
        $dto->starts_at = $startTime;
        $dto->ends_at = $endTime;
        $dto->type = LessonType::LESSON;
        $dto->name = $this->generateCourseLessonName($schedule->course);

        return $this->getRepository()->create($dto);
    }

    /**
     * @param Dto $dto
     * @return Lesson
     * @throws \Throwable
     */
    public function create(DtoWithUser $dto): Model
    {
        if ($dto->type === LessonType::LESSON) {
            $course = $this->courses->find($dto->course_id);
            $dto->name = $this->generateCourseLessonName($course);
            if (null === $dto->instructor_id) {
                $dto->instructor_id = $course->instructor_id;
            }
        } else {
            $dto->name = \translate('lesson', LessonType::LESSON);
        }

        $dto->branch_id = $this->getBranchIdByClassroomId($dto->classroom_id);
        $dto->schedule_id = null;

        return parent::create($dto);
    }

    public function checkIfScheduleLessonExist(Schedule $schedule, Carbon $date): bool
    {
        $startTime = $this->getDateAndTime($date, Carbon::parse($schedule->starts_at));
        $endTime =  $this->getDateAndTime($date, Carbon::parse($schedule->ends_at));

        return $this->getRepository()->checkIfScheduleLessonExist(
            $schedule->id, $startTime->toDateTimeString(), $endTime->toDateTimeString()
        );
    }

    /**
     * @param LessonsOnDate $lessonsOnDate
     * @return Collection<Lesson>
     */
    public function generateAndGetLessonsOnDate(LessonsOnDate $lessonsOnDate): Collection
    {
        $job = new GenerateLessonsOnDateJob($lessonsOnDate->date);
        dispatch($job);

        return $this->getRepository()->getLessonsOnDate($lessonsOnDate->date);
    }

    /**
     * @param LessonsFiltered $lessonsFiltered
     * @return Collection<Lesson>
     */
    public function generateAndGetLessonsFiltered(LessonsFiltered $lessonsFiltered): Collection
    {
        $job = new GenerateLessonsOnDateJob($lessonsFiltered->date);
        dispatch($job);

        return $this->getRepository()->getLessonsFiltered($lessonsFiltered, ['instructor', 'course', 'controller']);
    }

    protected function getBranchIdByClassroomId(string $classroomId): string
    {
        return $this->classrooms->getBranchIdByClassroomId($classroomId);
    }

    protected function generateCourseLessonName(Course $course): string
    {
        return \sprintf(
            '%s %s',
            \translate('lesson', LessonType::LESSON),
            $course->name
        );
    }

    protected function getDateAndTime(Carbon $date, Carbon $time): Carbon
    {
        return $date->clone()
            ->setHour($time->hour)
            ->setMinute($time->minute);
    }
}