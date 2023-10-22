<?php

namespace App\Services\Import;

use App\Components\Loader;
use App\Components\Student\Dto as StudentDto;
use App\Models\Person;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportStudentsService extends ImportService
{

    protected string $table = 'clients';
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

    protected function importRecord(\stdClass $record): void
    {
        $tag = '#' . $record->id . ' (' . $record->lastname . ' ' . $record->name . ')';
        if (empty($record->lastname) || empty($record->name) || empty($record->phone) ||
            empty($record->birthdate) || empty($record->sex) || !in_array(strtolower($record->sex), ['f', 'm'], true)) {
            $this->skipped($tag, 'Student has empty required fields');
            return;
        }

        try {
            $person = $this->mapPerson($record);
            if ($this->checkIfPersonExists($person)) {
                $this->skipped($tag, 'Student already exists');
                return;
            }
        } catch (\Throwable $e) {
            $this->skipped($tag, "Failed to import student: {$e->getMessage()}");
            return;
        }

        DB::beginTransaction();
        try {
            Loader::people()->getRepository()->save($person);
            $student = $this->createStudent($person);
            DB::commit();
            $this->imported($student->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->skipped($tag, "Failed to import student: {$e->getMessage()}");
        }
    }

    protected function mapPerson(\stdClass $record): Person
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

    protected function checkIfPersonExists(Person $person): bool
    {
        $people = Loader::people();
        return
            $people->getByPhoneNumber($person->phone) ||
            $people->getByNameGenderAndBirthDate(
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
        return Loader::students()->createFromPerson($dto, $person);
    }

}