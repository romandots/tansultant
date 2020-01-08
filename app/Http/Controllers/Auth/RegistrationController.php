<?php
/**
 * File: RegistrationController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\MemberApi\RegisterUserRequest;
use App\Http\Requests\MemberApi\VerifyPhoneNumberRequest;
use App\Http\Resources\UserResource;
use App\Services\UserRegister\UserRegisterService;
use App\Services\Verify\Exceptions\VerificationCodeIsInvalid;
use App\Services\Verify\VerificationService;

/**
 * Class RegistrationController
 * @package App\Http\Controllers\Auth
 */
class RegistrationController extends Controller
{
    private UserRegisterService $registrationService;

    private VerificationService $verificationService;

    public function __construct(
        UserRegisterService $userRegistrationService,
        VerificationService $verificationService
    ) {
        $this->registrationService = $userRegistrationService;
        $this->verificationService = $verificationService;
    }

    /**
     * Самостоятельная регистрация всех пользователей (менеджеров, преподавателей, студентов)
     * осуществляется через этот метод с указанием типа пользователя
     * - При первом запросе требуется указать номер телефона - на него будет отправлен код подтверждения
     * - При втором запросе необходимо отправить номер телефона и корректный код подтверждения
     *   (проверить корректность кода можно в методе checkVerificationCode)
     *
     * Будет создан пользователь (User), а также запись выбранного типа (инструктор или студент).
     * Будут запушены события UserRegisteredEvent, UserCreatedEvent,
     * InstructorCreatedEvent (при необходимости) и StudentCreatedEvent (при необходимости)
     *
     * @param RegisterUserRequest $request
     * @return UserResource|\Illuminate\Http\JsonResponse
     * @throws VerificationCodeIsInvalid
     * @throws \Exception
     */
    public function registerUser(RegisterUserRequest $request)
    {
        $registerUser = $request->getDto();

        // Verify phone number
        $verified = $this->registrationService->verifyUserPhoneNumber($registerUser);

        if (!$verified) {
            return \json_response([
                'message' => 'verification_code_sent',
            ]);
        }

        $newUser = $this->registrationService->registerUser($registerUser);

        return new UserResource($newUser);
    }

    /**
     * Проверка кода подтверждения при верификации номера телефона
     *
     * @param VerifyPhoneNumberRequest $request
     * @throws VerificationCodeIsInvalid
     */
    public function checkVerificationCode(VerifyPhoneNumberRequest $request): void
    {
        $dto = $request->getDto();
        $this->verificationService->checkVerificationCode($dto->phone, $dto->verification_code);
    }
}
