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
use App\Http\Requests\UserApi\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Repository\UserRepository;
use App\Services\UserRegister\UserRegisterService;

class RegistrationController extends Controller
{
    private UserRepository $userRepository;

    private UserRegisterService $registrationService;

    public function __construct(UserRepository $userRepository, UserRegisterService $userRegistrationService)
    {
        $this->userRepository = $userRepository;
        $this->registrationService = $userRegistrationService;
    }

    /**
     * @param RegisterUserRequest $request
     * @return UserResource|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function registerUser(RegisterUserRequest $request)
    {
        $registerUser = $request->getDto();
        $verified = $this->registrationService->verifyUserPhoneNumber($registerUser);

        if (!$verified) {
            return \json_response([
                'message' => 'verification_code_sent',
            ]);
        }

        $newUser = $this->registrationService->registerUser($registerUser);

        return new UserResource($newUser);
    }
}
