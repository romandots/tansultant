<?php

namespace App\Services\Import;

use App\Components\Loader;
use App\Components\Student\Dto as StudentDto;
use App\Components\Student\Exceptions\StudentAlreadyExists;
use App\Models\Enum\StudentStatus;
use App\Models\Person;
use App\Models\Student;
use App\Services\Import\Maps\StudentsMap;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ImportStudentsService extends ImportService
{

    protected string $table = 'clients';
    protected string $mapClass = StudentsMap::class;
    protected ?int $fromId = null;
    protected ?int $toId = null;
    protected ?int $limit = null;
    protected ?Carbon $startLastSeenDate = null;

    protected function askDetails(): void
    {
        $this->fromId = $this->cli->ask('Start from ID (leave empty to start from the first record)', $this->fromId);
        $this->toId = $this->cli->ask('End before ID (leave empty to import till the last record)', $this->fromId);
        $this->limit = $this->cli->ask('Limit (leave empty to import all records)', $this->limit);
        $startLastSeen = $this->cli->ask(
            'Start last seen date (YYYY-MM-DD; leave empty to import all records)',
            $this->startLastSeenDate
        );
        $this->startLastSeenDate = $startLastSeen ? Carbon::parse($startLastSeen) : null;
    }

    protected function prepareImportQuery(): \Illuminate\Database\Query\Builder
    {
        return $this->dbConnection
            ->table($this->table)
            ->orderBy('id', 'desc')
            ->when($this->fromId, fn($query) => $query->where('id', '>=', $this->fromId))
            ->when($this->toId, fn($query) => $query->where('id', '<', $this->toId))
            ->when($this->limit, fn($query) => $query->limit($this->limit))
            ->when($this->startLastSeenDate, fn($query) => $query->where('last_visit', '>=', $this->startLastSeenDate));
    }

    protected function makePerson(\stdClass $record): Person
    {
        return new Person([
            'id' => \uuid(),
            'last_name' => $record->lastname,
            'first_name' => $record->name,
            'patronymic_name' => $record->middlename,
            'birth_date' => \Carbon\Carbon::parse($record->birthdate),
            'gender' => \App\Models\Enum\Gender::tryFrom(strtolower($record->sex)),
            'phone' => \normalize_phone_number($record->phone),
            'email' => null,
            'picture' => null,
            'picture_thumb' => null,
            'instagram_username' => null,
            'telegram_username' => null,
            'vk_uid' => null,
            'facebook_uid' => null,
            'note' => 'Импортировано из старой базы данных',
            'created_at' => \Carbon\Carbon::parse($record->registered),
            'updated_at' => \Carbon\Carbon::now(),
        ]);
    }

    protected function checkIfPersonExists(Person $person): ?Person
    {
        $people = Loader::people();
        $existingPerson = $people->getByPhoneNumber($person->phone);
        return $existingPerson ?? $people->getByNameGenderAndBirthDate(
                lastName: $person->last_name,
                firstName: $person->first_name,
                patronymicName: $person->patronymic_name ?? '',
                gender: $person->gender,
                birthDate: $person->birth_date
            );
    }

    protected function createStudent(Person $person): Student
    {
        $dto = new StudentDto();
        $dto->student_is_customer = $person->isLegalAge();
        $dto->status = StudentStatus::ACTIVE;
        $student = Loader::students()->createFromPerson($dto, $person);
        $student->status = StudentStatus::ACTIVE;
        Loader::students()->getRepository()->save($student);

        return $student;
    }

    protected function getTag(\stdClass $record): string
    {
        return '#' . $record->id . ' (' . $record->lastname . ' ' . $record->name . ')';
    }

    protected function processImportRecord(\stdClass $record): Model
    {
        if (empty($record->lastname) || empty($record->name) || empty($record->phone) ||
            empty($record->birthdate) || empty($record->sex) || !in_array(strtolower($record->sex), ['f', 'm'], true)) {
            throw new \InvalidArgumentException('Student has empty required fields');
        }

        $person = $this->makePerson($record);
        $existingPerson = $this->checkIfPersonExists($person);
        $person = $existingPerson ?? $person;

        DB::beginTransaction();
        try {
            if (null === $existingPerson) {
                Loader::people()->getRepository()->save($person);
            }
            $student = $this->createStudent($person);
        } catch (StudentAlreadyExists $studentAlreadyExists) {
            $student = $studentAlreadyExists->getStudent();
            $this->getMapper()->map($record->id, $student->id);
            throw $studentAlreadyExists;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $student;
    }
}