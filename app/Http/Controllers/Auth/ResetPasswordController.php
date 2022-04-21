<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\PasswordReset\Exceptions\UserHasNoPerson;
use App\Services\PasswordReset\PasswordResetService;

class ResetPasswordController extends Controller
{
    /**
     * @param PasswordResetService $service
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\Verification\Exception\VerificationCodeAlreadySentRecently
     * @throws \App\Services\Verification\Exception\VerificationCodeIsInvalid
     * @throws \App\Services\Verification\Exception\VerificationCodeWasSentTooManyTimes
     * @throws UserHasNoPerson
     * @throws \Exception
     */
    public function reset(PasswordResetService $service, ResetPasswordRequest $request): \Illuminate\Http\JsonResponse
    {
        $resetPassword = $request->getDto();

        // If no verification code - generate and send it
        if (null === $resetPassword->verification_code) {
            $service->resetPassword($resetPassword);
            return \json_response(\get_status_message('verification_code_sent'), 201);
        }

        $service->resetPassword($resetPassword);

        return \json_response(\get_status_message('new_password_sent'), 200);
    }
}
