<?php

namespace App\Services\Import\Pipes\Instructor;

use App\Components\Instructor\Dto;
use App\Components\Person\Dto as PersonDto;
use App\Models\Enum\Gender;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use App\Services\Import\Traits\PersonTrait;
use Carbon\Carbon;
use Closure;

class MapInstructorPersonEntity implements PipeInterface
{
    use PersonTrait;

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

        /** @var Dto $dto */
        $dto = $ctx->dto;

        try {
            $dto->person_id = $this->getPerson($personDto, $ctx)->id;
        } catch (\Throwable $throwable) {
            throw new ImportException("Ошибка создания профиля: {$throwable->getMessage()}");
        }

        return $next($ctx);
    }
}