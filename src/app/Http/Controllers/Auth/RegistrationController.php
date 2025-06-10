<?php
/**
 * File: RegistrationController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-5
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Components\User\Formatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Services\UserRegister\UserRegisterService;

/**
 * Class RegistrationController
 * @package App\Http\Controllers\Auth
 */
class RegistrationController extends Controller
{
    /**
     * Самостоятельная регистрация всех пользователей (менеджеров, преподавателей, студентов)
     * осуществляется через этот метод с указанием типа пользователя
     * Сначала необходимо подтвердить номер телефона через метод верификации. Затем ID кода подтверждения
     * нужно передать в данный метод вместо номера телефона (который будет извлечен по коду верификации).
     * Будет создан пользователь (User), а также запись выбранного типа (инструктор или студент).
     * Будут запушены события UserRegisteredEvent, UserCreatedEvent,
     * InstructorCreatedEvent (при необходимости) и StudentCreatedEvent (при необходимости)
     *
     * @param UserRegisterService $registrationService
     * @param RegisterUserRequest $request
     * @return Formatter
     * @throws \Throwable
     */
    public function registerUser(UserRegisterService $registrationService, RegisterUserRequest $request): Formatter
    {
        $registerUser = $request->getDto();
        $newUser = $registrationService->registerUser($registerUser);

        return new Formatter($newUser);
    }
}
