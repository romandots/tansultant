<?php

declare(strict_types=1);

namespace App\Components\VerificationCode;

use App\Common\BaseComponentFacade;
use App\Models\VerificationCode;

/**
 * @method Service getService()
 * @method Repository getRepository()
 * @method array suggest(?string $query, string|\Closure $labelField = 'name', string|\Closure $valueField = 'id', array $extraFields = [])
 * @method \Illuminate\Support\Collection<\App\Models\VerificationCode> getAll()
 * @method \Illuminate\Support\Collection<\App\Models\VerificationCode> search(PaginatedInterface $searchParams, array $relations = []):
 * @method array getMeta(\App\Common\Contracts\PaginatedInterface $searchParams)
 * @method \App\Models\VerificationCode create(Dto $dto, array $relations = [])
 * @method \App\Models\VerificationCode find(string $id, array $relations = [])
 * @method void findAndDelete(string $id)
 * @method \App\Models\VerificationCode findAndRestore(string $id, array $relations = [])
 * @method \App\Models\VerificationCode findAndUpdate(string $id, Dto $dto, array $relations = [])
 */
class Facade extends BaseComponentFacade
{
    public function __construct()
    {
        parent::__construct(Service::class);
    }

    public function findVerifiedById($id): VerificationCode
    {
        return $this->getRepository()->findVerifiedById($id);
    }

    public function findByPhoneNumberAndVerificationCode(
        string $phoneNumber,
        ?string $verificationCode
    ): VerificationCode {
        return $this->getRepository()->findByPhoneNumberAndVerificationCode($phoneNumber, $verificationCode);
    }

    public function getByPhoneNumberNotExpired(string $phoneNumber): ?VerificationCode
    {
        return $this->getRepository()->getByPhoneNumberNotExpired($phoneNumber);
    }

    public function countByPhoneNumber(string $phoneNumber): int
    {
        return $this->getRepository()->countByPhoneNumber($phoneNumber);
    }

    public function updateVerifiedAt(VerificationCode $verificationCode): void
    {
        $this->getRepository()->updateVerifiedAt($verificationCode);
    }

    public function deleteByPhoneNumber(string $phone): void
    {
        $this->getService()->deleteByPhoneNumber($phone);
    }

    public function removeOldRecords(): void
    {
        $this->getRepository()->removeOldRecords();
    }
}