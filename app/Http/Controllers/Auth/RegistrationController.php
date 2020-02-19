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
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserRegister\UserRegisterService;
use App\Services\Verify\Exceptions\VerificationCodeIsInvalid;

/**
 * Class RegistrationController
 * @package App\Http\Controllers\Auth
 */
class RegistrationController extends Controller
{
    private UserRegisterService $registrationService;

    public function __construct(UserRegisterService $userRegistrationService) {
        $this->registrationService = $userRegistrationService;
    }

    /**
     * Самостоятельная регистрация всех пользователей (менеджеров, преподавателей, студентов)
     * осуществляется через этот метод с указанием типа пользователя
     * Сначала необходимо подтвердить номер телефона через метод верификации. Затем ID кода подтверждения
     * нужно передать в данный метод вместо номера телефона (который будет извлечен по коду верификации).
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
        $newUser = $this->registrationService->registerUser($registerUser);

        return new UserResource($newUser);
    }
}
