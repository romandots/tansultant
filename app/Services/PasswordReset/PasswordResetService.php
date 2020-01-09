<?php
/**
 * File: PasswordResetService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2020-01-9
 * Copyright (c) 2020
 */

declare(strict_types=1);

namespace App\Services\PasswordReset;

use App\Http\Requests\ManagerApi\DTO\UpdateUser;
use App\Notifications\TextMessages\PasswordResetSmsNotification;
use App\Repository\UserRepository;
use App\Services\Verify\VerificationService;

class PasswordResetService
{
    private VerificationService $verificationService;

    private UserRepository $userRepository;

    /**
     * PasswordResetService constructor.
     * @param UserRepository $userRepository
     * @param VerificationService $verificationService
     */
    public function __construct(
        UserRepository $userRepository,
        VerificationService $verificationService
    ) {
        $this->userRepository = $userRepository;
        $this->verificationService = $verificationService;
    }

    /**
     * @param \App\Http\Requests\Auth\DTO\ResetPassword $resetPassword
     * @throws \App\Services\Verify\Exceptions\VerificationCodeAlreadySentRecently
     * @throws \App\Services\Verify\Exceptions\VerificationCodeIsInvalid
     * @throws \App\Services\Verify\Exceptions\VerificationCodeWasSentTooManyTimes
     * @throws \Exception
     */
    public function resetPassword(\App\Http\Requests\Auth\DTO\ResetPassword $resetPassword): void
    {
        // If no verification code - generate and send it
        if (null === $resetPassword->verification_code) {
            $this->verificationService->verifyPhoneNumber($resetPassword->username);
            return;
        }

        // Otherwise - check the code
        $this->verificationService->verifyPhoneNumber($resetPassword->username, $resetPassword->verification_code);

        // Phone owner verified (the exception would be thrown otherwise)

        $user = $this->userRepository->findByUsername($resetPassword->username);

        $updateUser = new UpdateUser();
        $updateUser->password = $this->generatePassword();

        $this->userRepository->update($user, $updateUser);

        // Send SMS with new password
        $user->person->notify(new PasswordResetSmsNotification($updateUser->password));
    }

    private function generatePassword(): string
    {
        $random = \str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ234567890');

        return \substr($random, 0, 8);
    }
}
