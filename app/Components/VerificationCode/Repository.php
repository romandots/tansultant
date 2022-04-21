<?php

declare(strict_types=1);

namespace App\Components\VerificationCode;

use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @method array getSearchableAttributes()
 * @method bool withSoftDeletes()
 * @method \Illuminate\Database\Eloquent\Builder getQuery()
 * @method VerificationCode make()
 * @method int countFiltered(\App\Common\Contracts\FilteredInterface $search)
 * @method \Illuminate\Database\Eloquent\Collection<VerificationCode> findFilteredPaginated(PaginatedInterface $search, array $withRelations = [])
 * @method VerificationCode find(string $id)
 * @method VerificationCode findTrashed(string $id)
 * @method void update($record, Dto $dto)
 * @method void delete(VerificationCode $record)
 * @method void restore(VerificationCode $record)
 * @method void forceDelete(VerificationCode $record)
 * @mixin \App\Common\BaseRepository
 */
class Repository extends \App\Common\BaseComponentRepository
{
    public function __construct() {
        parent::__construct(
            VerificationCode::class,
            ['name']
        );
    }

    /**
     * @param VerificationCode $record
     * @param Dto $dto
     * @return void
     */
    public function fill(Model $record, \App\Common\Contracts\DtoWithUser $dto): void
    {
        $record->name = $dto->name;
    }


    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|VerificationCode
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException<\Illuminate\Database\Eloquent\Model>
     */
    public function findVerifiedById($id): VerificationCode
    {
        return $this->getQuery()
            ->where('id', $id)
            ->where('expired_at', '>', Carbon::now())
            ->whereNotNull('verified_at')
            ->firstOrFail();
    }

    /**
     * @param string $phoneNumber
     * @param string|null $verificationCode
     * @return \Illuminate\Database\Eloquent\Model|VerificationCode
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByPhoneNumberAndVerificationCode(
        string $phoneNumber,
        ?string $verificationCode
    ): VerificationCode {
        return $this->getQuery()
            ->where('phone_number', $phoneNumber)
            ->where('verification_code', $verificationCode)
            ->whereNull('verified_at')
            ->firstOrFail();
    }

    /**
     * @param string $phoneNumber
     * @return \Illuminate\Database\Eloquent\Model|null|VerificationCode
     */
    public function getByPhoneNumberNotExpired(string $phoneNumber): ?VerificationCode
    {
        return $this->getQuery()
            ->where('phone_number', $phoneNumber)
            ->where('expired_at', '>', Carbon::now())
            ->first();
    }

    /**
     * @param string $phoneNumber
     * @return int
     */
    public function countByPhoneNumber(string $phoneNumber): int
    {
        $offset = Carbon::now()->subSeconds((int)\config('verification.cleanup_timeout', 600));
        return $this->getQuery()
            ->where('phone_number', $phoneNumber)
            ->where('created_at', '>', $offset)
            ->count();
    }

    public function deleteByPhoneNumber(string $phoneNumber): void
    {
        $this->getQuery()
            ->where('phone_number', $phoneNumber)
            ->delete();
    }

    /**
     * @param VerificationCode $verificationCode
     */
    public function updateVerifiedAt(VerificationCode $verificationCode): void
    {
        $verificationCode->verified_at = Carbon::now();
        $verificationCode->save();
    }

    public function removeOldRecords(): void
    {
        $offset = Carbon::now()->subSeconds((int)\config('verification.cleanup_timeout', 600));
        \DB::table(VerificationCode::TABLE)
            ->where('created_at', '<', $offset)
            ->delete();
    }
}