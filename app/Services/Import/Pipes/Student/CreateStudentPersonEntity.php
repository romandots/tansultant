<?php

namespace App\Services\Import\Pipes\Student;

use App\Components\Loader;
use App\Components\Person\Dto as PersonDto;
use App\Components\Person\Exceptions\PersonAlreadyExist;
use App\Components\Student\Dto as StudentDto;
use App\Exceptions\SimpleValidationException;
use App\Models\Enum\Gender;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Carbon\Carbon;
use Closure;

class CreateStudentPersonEntity implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        $personDto = new PersonDto();
        $personDto->last_name = $ctx->old->lastname;
        $personDto->first_name = $ctx->old->name;
        $personDto->patronymic_name = $ctx->old->middlename;
        $personDto->phone = $ctx->old->phone;
        $personDto->telegram_username = str_replace(['@', 'https://telegram.com/', 'https://t.me/'], '', $ctx->old->telegram);
        $personDto->note = $ctx->old->note;
        $personDto->gender = match(strtolower($ctx->old->sex)) {
            'm' => Gender::MALE,
            'f' => Gender::FEMALE,
            default => throw new ImportException("Не указан пол ({$ctx->old?->lastname} {$ctx->old?->name}): $ctx->old->sex"),
        };

        try {
            $personDto->birth_date = Carbon::createFromFormat('Y-m-d', $ctx->old->birthdate);
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            throw new ImportException("Невалидная дата рождения ({$ctx->old?->lastname} {$ctx->old?->name})");
        }

        try {
            $person = Loader::people()->create($personDto);
            $ctx->manager->increaseCounter('person');
            $ctx->debug("Создали профиль {$person->last_name} {$person->first_name} → #{$person->id}");
        } catch (PersonAlreadyExist $alreadyExist) {
            $person = $alreadyExist->getPerson();
        } catch (SimpleValidationException $validationException) {
            throw new ImportException("Ошибка валидации профиля ({$ctx->old?->lastname} {$ctx->old?->name}): {$validationException->field} {$validationException->rule}", $ctx->toArray());
        } catch (\Throwable $throwable) {
            throw new ImportException("Ошибка сохранения профиля ({$ctx->old?->lastname} {$ctx->old?->name}): {$throwable->getMessage()}", $ctx->toArray());
        }

        try {
            $person->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $ctx->old->registered);
            Loader::people()->getRepository()->save($person);
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            throw new ImportException("Невалидная дата регистрации ({$ctx->old?->lastname} {$ctx->old?->name})");
        }

        $ctx->dto = new StudentDto();
        $ctx->dto->person_id = $person->id;

        return $next($ctx);
    }
}