<?php

namespace App\Services\Import;

use App\Common\ValueObjects\DatePeriod;
use App\Components\Instructor\Exceptions\InstructorAlreadyExists;
use App\Components\Loader;
use App\Components\Person\Dto;
use App\Components\Person\Exceptions\PersonAlreadyExist;
use App\Models\Branch;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Enum\CourseStatus;
use App\Models\Enum\Gender;
use App\Models\Enum\InstructorStatus;
use App\Models\Enum\Weekday;
use App\Models\Instructor;
use App\Models\Price;
use App\Models\Schedule;
use App\Services\Import\Maps\ClassroomsMap;
use App\Services\Import\Maps\CoursesMap;
use App\Services\Import\Maps\InstructorsMap;
use App\Services\Import\Maps\ObjectsMap;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class ImportCoursesService extends ImportService
{
    protected string $table = 'classes';
    protected string $teachersTable = 'teachers';
    protected ?Carbon $startDate = null;

    protected ClassroomsMap $classroomsMap;
    private InstructorsMap $instructorsMap;
    protected string $mapClass = CoursesMap::class;

    private function getClassroomsMap(): ClassroomsMap
    {
        return $this->getMapper(ClassroomsMap::class);
    }

    protected function getTag(\stdClass $record): string
    {
        return '#' . $record->id . ' ' . $record->class_title;
    }

    protected function processImportRecord(\stdClass $record): Model
    {
        \DB::beginTransaction();

        try {
            $instructor = $this->findOrCreateInstructor($record);
            $course = $this->findOrCreateCourse($record, $instructor);

            if ($record->dancefloor_id !== null) {
                $classroom = $this->getClassroom($record->dancefloor_id);
                $this->createSlots($record, $classroom, $course);
            }
        } catch (\Throwable $e) {
            \DB::rollBack();
            throw $e;
        }

        \DB::commit();

        return $course;
    }

    protected function askDetails(): void
    {
        if ($this->getClassroomsMap()->isMapEmpty()) {
            $this->cli->error('Classrooms map is empty. Please import classrooms first.');
            exit;
        }

        $startDate = $this->cli->ask(
            'Start date (YYYY-MM-DD; leave empty to import all records)',
            $this->startDate
        );
        $this->startDate = $startDate ? Carbon::parse($startDate) : null;
    }

    protected function prepareImportQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->dbConnection
            ->table($this->table)
            ->where(function (Builder $query) {
                if ($this->startDate !== null) {
                    $query->whereRaw("{$this->table}.start_date IS NULL OR {$this->table}.start_date > '{$this->startDate->format('Y-m-d')}'");
                }
                $today = date('Y-m-d');
                $query->whereRaw("{$this->table}.end_date IS NULL OR {$this->table}.end_date >= '{$today}'");
            })
            ->orderBy('id', 'desc');
    }

    private function findOrCreateInstructor(\stdClass $record): Instructor
    {
        $instructor = $this->getInstructorsMap()->mappedRecord($record->teacher_id);
        if ($instructor instanceof Instructor) {
            return $instructor;
        }

        $teacher = $this->getInstructorsMap()->loadOldObject($record->teacher_id);
        if (null === $teacher) {
            $teacher = $this->getTeacher($record->teacher_id);
        }

        try {
            $newInstructor = $this->createInstructor($teacher);
            $this->getInstructorsMap()->map($record->teacher_id, $newInstructor->id);
        } catch (\Throwable $e) {
            $this->skipped(
                $this->getTag($record),
                "Failed to create instructor for teacher {$teacher->name} {$teacher->lastname}");
            throw $e;
        }

        return $newInstructor;
    }

    private function getInstructorsMap(): ObjectsMap
    {
        if (!isset($this->instructorsMap)) {
            $this->instructorsMap = new InstructorsMap(cli: $this->cli, dbConnection: $this->dbConnection);
        }

        return $this->instructorsMap;
    }

    private function getTeacher(int $teacherId): \stdClass
    {
        return $this->dbConnection
            ->table($this->teachersTable)
            ->where('id', $teacherId)
            ->first();
    }

    /**
     * @param \stdClass $record Old teacher record from DB
     * @return Instructor
     * @throws \Throwable
     */
    private function createInstructor(\stdClass $record): Instructor
    {
        $personDto = new Dto();
        $personDto->last_name = $record->lastname;
        $personDto->first_name = $record->name;
        $personDto->phone = $record->phone;
        $personDto->gender = Gender::tryFrom(strtolower((string)$record->sex)) ?? Gender::MALE;

        try {
            $personDto->birth_date = Carbon::parse($record->birthdate);
        } catch (\Exception $e) {
            $personDto->birth_date = Carbon::now()->subYears(30);
        }

        try {
            $person = Loader::people()->create($personDto);
        } catch (PersonAlreadyExist $e) {
            $person = $e->getPerson();
        }

        if ($person->load('instructors')->instructor instanceof Instructor) {
            return $person->instructor;
        }

        $instructorDto = new \App\Components\Instructor\Dto();
        $instructorDto->status = match ($record->status) {
            'exclusive', 'staff' => InstructorStatus::HIRED,
            'part-time' => InstructorStatus::FREELANCE,
            default => InstructorStatus::FIRED,
        };
        $instructorDto->display = true;

        try {
            return Loader::instructors()->createFromPerson($instructorDto, $person);
        } catch (InstructorAlreadyExists $exception) {
            $instructor = $exception->getInstructor();
            if ($instructor instanceof Instructor) {
                return $instructor;
            }

            throw $exception;
        }
    }

    private function findOrCreateCourse(\stdClass $record, Instructor $instructor): Course
    {
        $course = $this->getMapper()->mappedRecord($record->id);
        if ($course instanceof Course) {
            return $course;
        }

        return $this->createCourse($record, $instructor);
    }

    private function createCourse(\stdClass $record, Instructor $instructor): Course
    {
        $course = new Course([
            'id' => \uuid(),
            'name' => $record->class_title,
            'status' => CourseStatus::ACTIVE->value,
            'summary' => $record->description,
            'description' => $record->description,
            'display' => $record->hidden !== 1,
            'picture' => null,
            'picture_thumb' => null,
            'age_restrictions' => $this->parseAgeRestrictions($record->age_restrictions),
            'starts_at' => $record->start_date ? Carbon::parse($record->start_date) : Carbon::now(),
            'ends_at' => $record->end_date ? Carbon::parse($record->end_date) : null,
            'instructor_id' => $instructor->id,
        ]);

        Loader::courses()->getRepository()->save($course);

        return $course;
    }

    private function createScheduleSlot(Branch $branch, Classroom $classroom, Course $course, Price $price, Weekday $weekday, DatePeriod $period): Schedule
    {
        $slot = Loader::schedules()->getRepository()->make([
            'id' => \uuid(),
            'cycle' => \App\Models\Enum\ScheduleCycle::EVERY_WEEK,
            'branch_id' => $branch->id,
            'classroom_id' => $classroom->id,
            'course_id' => $course->id,
            'price_id' => $price->id,
            'starts_at' => $period->start,
            'ends_at' => $period->end,
            'weekday' => $weekday->value,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
            'deleted_at' => null,
        ]);

        Loader::schedules()->findOrSave($slot, ['cycle', 'branch_id', 'classroom_id', 'course_id', 'weekday', 'starts_at', 'ends_at']);

        return $slot;
    }

    private function getClassroom(int $dancefloorId): Classroom
    {
        $classroom = $this->getClassroomsMap()->mappedRecord($dancefloorId);
        if ($classroom instanceof Classroom) {
            return $classroom;
        }

        throw new Exceptions\ImportServiceException('Classroom not created');
    }

    private function parseAgeRestrictions(?string $age_restrictions): array
    {
        if (preg_match('/^(\d+)\+$/', $age_restrictions, $matches)) {
            return ['from' => (int)$matches[1], 'to' => null];
        }

        if (preg_match('/^(\d+)\s*\-\s*(\d+)$/', $age_restrictions, $matches)) {
            return ['from' => (int)$matches[1], 'to' => (int)$matches[2]];
        }

        return ['from' => null, 'to' => null];
    }

    private function createSlots(\stdClass $record, Classroom $classroom, Course $course): Collection
    {
        $slots = new Collection();
        $branch = $classroom->branch;
        $weekdays = $this->parseWeekdays($record);
        $price = $this->findOrCreatePrice((int)$record->price_rate);
        foreach ($weekdays as $weekday => $period) {
            $slots->push(
                $this->createScheduleSlot($branch, $classroom, $course, $price, Weekday::from($weekday), $period)
            );
        }

        return $slots;
    }

    /**
     * @param \stdClass $record
     * @param array<int, DatePeriod>
     */
    private function parseWeekdays(\stdClass $record): array
    {
        $weekdays = [];
        foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $i => $name) {
            if (isset($record->{$name}) && $record->periods > 0) {
                $weekday = Weekday::tryFrom($i + 1);
                if ($weekday) {
                    $startsAt = Carbon::parse($record->{$name});
                    $weekdays[$weekday->value] = new DatePeriod($startsAt, ($record->periods) * 30);
                }
            }
        }

        return array_filter($weekdays);
    }

    private function findOrCreatePrice(int $priceValue): Price
    {
        try {
            $price = Loader::prices()->findByName($priceValue);
        } catch (ModelNotFoundException) {
            try {
                $price = Loader::prices()->findByPriceValue($priceValue);
            } catch (ModelNotFoundException) {
                $price = Loader::prices()->make([
                    'id' => \uuid(),
                    'name' => $priceValue,
                    'price' => $priceValue,
                ]);
                Loader::prices()->findOrSave($price, ['name', 'price']);
            }
        }

        return $price;
    }

}