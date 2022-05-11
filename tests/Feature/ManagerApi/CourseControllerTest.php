<?php

namespace Tests\Feature\ManagerApi;

use App\Common\DTO\DtoWithUser;
use App\Common\DTO\SearchFilterDto;
use App\Components\Course\Dto;
use App\Components\Loader;
use App\Models\Course;
use App\Models\Enum\CourseStatus;
use App\Services\Permissions\CoursesPermission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CourseControllerTest extends AdminControllerTest
{
    private \App\Models\Instructor $secondInstructor;

    public function setUp(): void
    {
        $this->facade = Loader::courses();
        $this->baseRoutePrefix = 'courses';
        $this->permissionClass = CoursesPermission::class;
        parent::setUp();
        $this->secondInstructor = $this->createFakeInstructor();
    }

    /**
     * @param array $attributes
     * @return Course
     */
    protected function createRecord(array $attributes): Model
    {
        return $this->createFakeCourse($attributes);
    }

    /**
     * @return Dto
     */
    protected function createDto(): DtoWithUser
    {
        $dto = new Dto();
        $dto->name = 'Тестовый курс';
        $dto->status = CourseStatus::ACTIVE;
        $dto->description = 'Some description';
        $dto->display = true;
        $dto->age_restrictions = ['from' => 3, 'to' => 12];
        $dto->instructor_id = $this->createFakeInstructor()->id;
        $dto->starts_at = Carbon::yesterday();
        $dto->ends_at = Carbon::tomorrow();

        return $dto;
    }

    protected function getCreateAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'name' => $this->dto->name,
            'status' => $this->dto->status->value,
            'description' => $this->dto->description,
            'display' => $this->dto->display,
            'age_restrictions_from' => $this->dto->age_restrictions['from'],
            'age_restrictions_to' => $this->dto->age_restrictions['to'],
            'instructor_id' => $this->dto->instructor_id,
            'starts_at' => $this->dto->starts_at,
            'ends_at' => $this->dto->ends_at,
        ];
    }

    protected function getAttributes(): array
    {
        assert($this->dto instanceof Dto);
        return [
            'name' => $this->dto->name,
            'status' => $this->dto->status->value,
            'description' => $this->dto->description,
            'display' => $this->dto->display,
            'age_restrictions' => $this->dto->age_restrictions,
            'instructor_id' => $this->dto->instructor_id,
            'starts_at' => $this->dto->starts_at,
            'ends_at' => $this->dto->ends_at,
        ];
    }

    protected function getUpdateAttributes(): array
    {
        return [
            'name' => 'Другой тестовый курс',
            'status' => CourseStatus::DISABLED->value,
            'description' => 'Description',
            'display' => false,
            'age_restrictions_from' => 10,
            'age_restrictions_to' => 14,
            'instructor_id' => $this->secondInstructor->id,
            'starts_at' =>  Carbon::now()->addDays(1),
            'ends_at' => Carbon::now()->addDays(5),
        ];
    }

    protected function getAlternateAttributes(): array
    {
        return [
            'name' => 'Другой тестовый курс',
            'status' => CourseStatus::DISABLED->value,
            'description' => 'Description',
            'display' => false,
            'age_restrictions' => ['from' => 10, 'to' => 14],
            'instructor_id' => $this->secondInstructor->id,
            'starts_at' =>  Carbon::now()->addDays(1),
            'ends_at' => Carbon::now()->addDays(5),
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