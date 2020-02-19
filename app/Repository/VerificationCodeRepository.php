<?php
/**
 * File: VerificationCodeRepository.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Repository;

use App\Models\VerificationCode;
use Carbon\Carbon;

/**
 * Class VerificationCodeRepository
 * @package App\Repository
 */
class VerificationCodeRepository
{
    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|VerificationCode
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findVerifiedById($id): VerificationCode
    {
        return VerificationCode::query()
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
        return VerificationCode::query()
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
        return VerificationCode::query()
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
        return VerificationCode::query()
            ->where('phone_number', $phoneNumber)
            ->where('created_at', '>', $offset)
            ->count();
    }

    /**
     * @param string $phoneNumber
     * @param string $code
     * @return VerificationCode
     * @throws \Exception
     */
    public function create(string $phoneNumber, string $code): VerificationCode
    {
        $timeout = (int)\config('verification.timeout', 60);

        $verificationCode = new VerificationCode();
        $verificationCode->id = \uuid();
        $verificationCode->created_at = Carbon::now();
        $verificationCode->expired_at = Carbon::now()->addSeconds($timeout);
        $verificationCode->phone_number = $phoneNumber;
        $verificationCode->verification_code = $code;
        $verificationCode->save();

        return $verificationCode;
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

    public function removeRecordsByPhone(string $phone)
    {
        \DB::table(VerificationCode::TABLE)
            ->where('phone_number', $phone)
            ->delete();
    }
}
