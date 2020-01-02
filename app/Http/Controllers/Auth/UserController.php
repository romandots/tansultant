<?php
/**
 * File: UserController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerApi\UpdateUserPasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function me(Request $request): UserResource
    {
        $user = $request->user();
        $user->load('person');

        return new UserResource($user);
    }

    public function updatePassword(UpdateUserPasswordRequest $request): void
    {
        $user = $request->user();
        $dto = $request->getDto();
        $this->userService->updatePassword($user, $dto);
    }
}
