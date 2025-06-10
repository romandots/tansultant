<?php

namespace App\Services\Import\Pipes\Student;

use App\Components\Customer\Dto as CustomerDto;
use App\Components\Customer\Exceptions\CustomerAlreadyExists;
use App\Components\Loader;
use App\Components\Student\Dto as StudentDto;
use App\Models\Person;
use App\Services\Import\Contracts\PipeInterface;
use App\Services\Import\Exceptions\ImportException;
use App\Services\Import\ImportContext;
use Carbon\Carbon;
use Closure;

class CreateStudentCustomerEntity implements PipeInterface
{

    public function handle(ImportContext $ctx, Closure $next): ImportContext
    {
        /** @var StudentDto $dto */
        $dto = $ctx->dto;
        /** @var Person $person */
        $person = Loader::people()->findById($dto->person_id);

        $dto->student_is_customer = $person->isLegalAge();

        if (!$dto->student_is_customer) {
            $ctx->debug("Студент несовершеннолетний -- пропускаем создание клиента");

            return $next($ctx);
        }

        try {
            $customer = Loader::customers()->createFromPerson(new CustomerDto($ctx->adminUser), $person);
            $ctx->manager->increaseCounter('customer');
            $ctx->debug("Создали клиента {$customer->name} → #{$customer->id}");
        } catch (CustomerAlreadyExists $alreadyExists) {
            $customer = $alreadyExists->getCustomer();
        } catch (\Throwable $throwable) {
            throw new ImportException("Ошибка сохранения клиента ({$ctx->old?->lastname} {$ctx->old?->name}): {$throwable->getMessage()}", $ctx->toArray());
        }

        try {
            $customer->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $ctx->old->registered);
        } catch (\Carbon\Exceptions\InvalidFormatException) {
            throw new ImportException("Невалидная дата регистрации ({$ctx->old?->lastname} {$ctx->old?->name})");
        }
        try {
            $customer->seen_at = Carbon::createFromFormat('Y-m-d H:i:s', $ctx->old->last_visit);
        } catch (\Carbon\Exceptions\InvalidFormatException) {
        }

        Loader::customers()->getRepository()->save($customer);

        $dto->customer_id = $customer->id;

        return $next($ctx);
    }
}