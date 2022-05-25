<?php

namespace Tests\Feature\ManagerApi;

use App\Common\DTO\DtoWithUser;
use App\Common\DTO\SearchFilterDto;
use App\Components\Lesson\Dto;
use App\Components\Loader;
use App\Models\Enum\LessonStatus;
use App\Models\Enum\LessonType;
use App\Models\Lesson;
use App\Services\Permissions\LessonsPermission;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Dto $dto
 */
class LessonControllerTest extends AdminControllerTest
{
    private \App\Models\Schedule $secondSchedule;
    private \App\Models\Instructor $secondInstructor;
    private string $dateFormat;

    public function setUp(): void
    {
        $this->facade = Loader::lessons();
        $this->baseRoutePrefix = 'lessons';
        $this->permissionClass = LessonsPermission::class;
        parent::setUp();

        $this->dateFormat = 'Y-m-d H:i:s';
        $this->secondSchedule = $this->createFakeSchedule([
            'starts_at' => '12:00:00',
            'ends_at' => '13:00:00',
        ]);
        $this->secondInstructor = $this->createFakeInstructor();
    }

    /**
     * @param array $attributes
     * @return Lesson
     */
    protected function createRecord(array $attributes): Model
    {
        return $this->createFakeLesson($attributes);
    }

    /**
     * @return Dto
     */
    protected function createDto(): DtoWithUser
    {
        $fakeSchedule = $this->createFakeSchedule([
            'starts_at' => '10:00:00',
            'ends_at' => '11:30:00',
        ]);

        $dto = new Dto();
        $dto->name = 'Тест';
        $dto->status = LessonStatus::BOOKED;
        $dto->type = LessonType::LESSON;
        $dto->branch_id = $fakeSchedule->branch_id;
        $dto->starts_at = $fakeSchedule->starts_at;
        $dto->ends_at = $fakeSchedule->ends_at;
        $dto->course_id = $fakeSchedule->course_id;
        $dto->classroom_id = $fakeSchedule->classroom_id;
        $dto->instructor_id = $this->createFakeInstructor()->id;

        return $dto;
    }

    protected function getCreateAttributes(): array
    {
        return $this->getAttributes();
    }

    protected function getAttributes(): array
    {
        return [
            'status' => $this->dto->status->value,
            'type' => $this->dto->type->value,
            'branch_id' => $this->dto->branch_id,
            'starts_at' => $this->dto->starts_at->format($this->dateFormat),
            'ends_at' => $this->dto->ends_at->format($this->dateFormat),
            'course_id' => $this->dto->course_id,
            'instructor_id' => $this->dto->instructor_id,
            'classroom_id' => $this->dto->classroom_id,
        ];
    }

    protected function getUpdateAttributes(): array
    {
        return $this->getAlternateAttributes();
    }

    protected function getAlternateAttributes(): array
    {
        return [
            'status' => LessonStatus::BOOKED->value, // can't change that
            'type' => LessonType::LESSON->value,
            'branch_id' => $this->secondSchedule->branch_id,
            'starts_at' => $this->secondSchedule->starts_at->format($this->dateFormat),
            'ends_at' => $this->secondSchedule->ends_at->format($this->dateFormat),
            'course_id' => $this->secondSchedule->course_id,
            'instructor_id' => $this->secondInstructor->id,
            'classroom_id' => $this->secondSchedule->classroom_id,
        ];
    }

    public function testSearch(): void
    {
        $dto = new SearchFilterDto();
        $dto->query = 'Тестовый зал';
        $this->search($dto);
    }

    public function testSuggest(): void
    {
        $this->suggest();
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

    public function testRestore(): void
    {
        $this->restore();
    }
}