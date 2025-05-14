<?php

namespace App\Services\Import\Pipes\Instructor;

use App\Components\Instructor\Dto;
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
        $personDto = new PersonDto($ctx->adminUser);
        $personDto->last_name = $ctx->old->lastname;
        $personDto->first_name = $ctx->old->name;
        $personDto->patronymic_name = $ctx->old->nickname;
        $personDto->phone = $ctx->old->phone;
        $personDto->gender = match($ctx->old->sex) {
            'm' => Gender::MALE,
            'f' => Gender::FEMALE,
            default => throw new ImportException("Не указан пол ({$ctx->old?->name})"),
        };
        try {
            $personDto->birth_date = Carbon::createFromFormat('Y-m-d', $ctx->old->birthdate);
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            throw new ImportException("Невалидная дата рождения ({$ctx->old?->name}): {$ctx->old->birthdate}");
        }

        try {
            $person = Loader::people()->create($personDto);
            $ctx->manager->increaseCounter('person');
            $ctx->debug("Создали профиль {$person->last_name} {$person->first_name} → #{$person->id}");
        } catch (PersonAlreadyExist $alreadyExist) {
            $person = $alreadyExist->getPerson();
        }

        /** @var Dto $dto */
        $dto = $ctx->dto;
        $dto->person_id = $person->id;

        return $next($ctx);
    }
}