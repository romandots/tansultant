<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\PasswordReset\PasswordResetService;

class ResetPasswordController extends Controller
{
    /**
     * @param PasswordResetService $service
     * @param ResetPasswordRequest $request
     * @throws \App\Services\Verify\Exceptions\VerificationCodeAlreadySentRecently
     * @throws \App\Services\Verify\Exceptions\VerificationCodeIsInvalid
     * @throws \App\Services\Verify\Exceptions\VerificationCodeWasSentTooManyTimes
     * @throws \Exception
     */
    public function reset(PasswordResetService $service, ResetPasswordRequest $request): void
    {
        $resetPassword = $request->getDto();
        $service->resetPassword($resetPassword);
    }
}
