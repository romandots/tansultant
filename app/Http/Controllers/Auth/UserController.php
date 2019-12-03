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

/**
 * Class UserController
 * @package App\Http\Controllers\Auth
 */
class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return UserResource
     */
    public function me(Request $request): UserResource
    {
        $user = $request->user();
        $user->load('person');

        return new UserResource($user);
    }

    /**
     * @param UpdateUserPasswordRequest $request
     */
    public function updatePassword(UpdateUserPasswordRequest $request): void
    {
        $user = $request->user();
        $dto = $request->getDto();
        $this->userService->updatePassword($user, $dto);
    }
}
