<?php

namespace App\Services\Import\Pipes\Instructor;

use App\Components\Instructor\Dto as InstructorDto;
use App\Components\Loader;
use App\Components\Person\Dto as PersonDto;
use App\Components\Person\Exceptions\PersonAlreadyExist;
use App\Models\Enum\Gender;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Carbon\Carbon;
use Closure;

class MapInstructorPersonEntity implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $personDto = new PersonDto();
        $personDto->last_name = $ctx->old->lastname;
        $personDto->first_name = $ctx->old->name;
        $personDto->phone = $ctx->old->phone;
        $personDto->gender = match($ctx->old->sex) {
            'm' => Gender::MALE,
            'f' => Gender::FEMALE,
            default => throw new ImportException("Не указан пол ({$ctx->old?->name})"),
        };
        try {
            $personDto->birth_date = Carbon::createFromFormat('Y-m-d', $ctx->old->birthdate);
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            throw new ImportException("Невалидная дата рождения ({$ctx->old?->name})");
        }

        try {
            $person = Loader::people()->create($personDto);
            $ctx->manager->increaseCounter('person');
        } catch (PersonAlreadyExist $alreadyExist) {
            $person = $alreadyExist->getPerson();
        }

        $ctx->dto = new InstructorDto();
        $ctx->dto->person_id = $person->id;

        return $next($ctx);
    }
}