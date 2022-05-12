<?php

namespace Tests\Feature\ManagerApi;

use App\Common\DTO\DtoWithUser;
use App\Components\Loader;
use App\Components\Schedule\Dto;
use App\Models\Enum\ScheduleCycle;
use App\Models\Enum\Weekday;
use App\Models\Schedule;
use App\Services\Permissions\SchedulesPermission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ScheduleControllerTest extends AdminControllerTest
{
    private \App\Models\Branch $secondBranch;
    private \App\Models\Classroom $secondClassroom;
    private \App\Models\Course $secondCourse;

    public function setUp(): void
    {
        $this->facade = Loader::schedules();
        $this->baseRoutePrefix = 'schedules';
        $this->permissionClass = SchedulesPermission::class;
        parent::setUp();

        $this->secondBranch = $this->createFakeBranch();
        $this->secondClassroom = $this->createFakeClassroom(['branch_id' => $this->secondBranch->id]);
        $this->secondCourse = $this->createFakeCourse();
    }

    /**
     * @param array $attributes
     * @return Schedule
     */
    protected function createRecord(array $attributes): Model
    {
        return $this->createFakeSchedule($attributes);
    }

    /**
     * @return Dto
     */
    protected function createDto(): DtoWithUser
    {
        $dto = new Dto();
        $branch = $this->createFakeBranch();
        $classroom = $this->createFakeClassroom(['branch_id' => $branch->id]);
        $course = $this->createFakeCourse();
        $dto->branch_id = $branch->id;
        $dto->classroom_id = $classroom->id;
        $dto->course_id = $course->id;
        $dto->starts_at = Carbon::now()->setHour(12)->setMinute(0)->setSecond(0);
        $dto->ends_at = Carbon::now()->setHour(13)->setMinute(0)->setSecond(0);
        $dto->from_date = Carbon::today();
        $dto->to_date = Carbon::tomorrow();
        $dto->cycle = ScheduleCycle::EVERY_WEEK;
        $dto->weekday = Weekday::MONDAY;

        return $dto;
    }

    protected function getAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'branch_id' => $this->dto->branch_id,
            'classroom_id' => $this->dto->classroom_id,
            'course_id' => $this->dto->course_id,
            'starts_at' => $this->dto->starts_at->format('H:i:s'),
            'ends_at' => $this->dto->ends_at->format('H:i:s'),
            'from_date' => $this->dto->from_date->format('Y-m-d'),
            'to_date' => $this->dto->to_date->format('Y-m-d'),
            'cycle' => $this->dto->cycle->value,
            'weekday' => $this->dto->weekday->value,
        ];
    }

    protected function getAlternateAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'branch_id' => $this->secondBranch->id,
            'classroom_id' => $this->secondClassroom->id,
            'course_id' => $this->secondCourse->id,
            'starts_at' => Carbon::now()->setHour(18)->setMinute(0)->setSecond(0)->format('H:i:s'),
            'ends_at' => Carbon::now()->setHour(19)->setMinute(30)->setSecond(0)->format('H:i:s'),
            'from_date' => Carbon::now()->format('Y-m-d'),
            'to_date' => Carbon::now()->format('Y-m-d'),
            'cycle' => ScheduleCycle::EVERY_MONTH->value,
            'weekday' => null,
        ];
    }

    public function testStore(): void
    {
        $this->store();
    }

    public function testUpdate(): void
    {
        $this->update();
    }

    public function testDestroy(): void
    {
        $this->destroy();
    }
}