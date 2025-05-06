<?php

namespace App\Services\Import\Pipes\Student;

use App\Components\Loader;
use App\Components\Student\Dto as StudentDto;
use App\Models\Enum\StudentStatus;
use App\Models\Person;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Carbon\Carbon;
use Closure;

class CreateStudentEntity implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        /** @var StudentDto $dto */
        $dto = $ctx->dto;
        $dto->status = StudentStatus::ACTIVE;

        /** @var Person $person */
        $person = Loader::people()->findById($dto->person_id);

        /** @var class-string<\Illuminate\Database\Eloquent\Model> $modelClass */
        $modelClass = $ctx->manager->mapKeyToModel($ctx->entity);
        try {
            $student = Loader::students()->createFromPerson($dto, $person);
        } catch (\Throwable $throwable) {
            throw new ImportException("Ошибка сохранения записи: {$throwable->getMessage()}", $ctx->getErrorContext());
        }

        try {
            $student->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $ctx->old->registered);
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            throw new ImportException("Невалидная дата регистрации ({$ctx->old?->lastname} {$ctx->old?->name})");
        }

        try {
            $student->seen_at = Carbon::createFromFormat('Y-m-d H:i:s', $ctx->old->last_visit);
        } catch (\Carbon\Exceptions\InvalidFormatException) {
        }

        $student->status = match($ctx->old->status) {
            'active' => StudentStatus::ACTIVE,
            'recent' => StudentStatus::RECENT,
            default => StudentStatus::FORMER,
        };

        Loader::students()->getRepository()->save($student);

        if (!assert($student instanceof $modelClass)) {
            throw new ImportException("Модель {$ctx->entity} не является экземпляром {$modelClass}", $ctx->getErrorContext());
        }

        $ctx->mapNewId($student->id);

        $ctx->logger->debug("Сохранили новую запись сущности {$ctx->entity} с ID #{$ctx->newId}");

        return $ctx;
    }
}