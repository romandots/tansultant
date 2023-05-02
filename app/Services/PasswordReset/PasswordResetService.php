<?php
/**
 * File: PasswordResetService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Services\PasswordReset;

use App\Common\BaseService;
use App\Components\Loader;
use App\Components\User\UpdateUserPasswordDto;
use App\Services\Verification\VerificationService;

class PasswordResetService extends BaseService
{
    private VerificationService $verificationService;
    private \App\Components\User\Facade $users;

    public function __construct(
        VerificationService $verificationService
    ) {
        $this->users = Loader::users();
        $this->verificationService = $verificationService;
    }

    /**
     * @param \App\Http\Requests\Auth\DTO\ResetPassword $resetPassword
     * @throws \App\Services\Verification\Exception\VerificationCodeAlreadySentRecently
     * @throws \App\Services\Verification\Exception\VerificationCodeIsInvalid
     * @throws \App\Services\Verification\Exception\VerificationCodeWasSentTooManyTimes
     * @throws Exceptions\UserHasNoPerson
     * @throws \Exception
     */
    public function resetPassword(\App\Http\Requests\Auth\DTO\ResetPassword $resetPassword): void
    {
        $user = $this->users->findByUsername($resetPassword->username);

        if (null === $user->person) {
            throw new Exceptions\UserHasNoPerson();
        }

        $phoneNumber = \normalize_phone_number($user->person->phone);

        // If no verification code - generate and send it
        if (null === $resetPassword->verification_code) {
            $this->verificationService->initNewVerificationCode($phoneNumber);
            return;
        }

        // Otherwise - check the code
        $this->verificationService->checkVerificationCode($phoneNumber, $resetPassword->verification_code);

        // Phone owner verified (the exception would be thrown otherwise)

        $updateUserPasswordDto = new UpdateUserPasswordDto();
        $updateUserPasswordDto->new_password = $this->generatePassword();
        $updateUserPasswordDto->skip_check = true;

        \DB::transaction(function () use ($user, $updateUserPasswordDto, $phoneNumber) {
            // Save new password
            $this->users->updatePassword($user, $updateUserPasswordDto);

            // Remove verification codes
            $this->verificationService->cleanUp($phoneNumber);

            // Send SMS with new password
            Loader::notifications()->notify(
                $user->person,
                \trans('password_reset.new_password_text_message', ['new_password' => $updateUserPasswordDto->new_password])
            );
        });
    }

    private function generatePassword(): string
    {
        $random = \str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890');

        return \substr($random, 0, 8);
    }
}
