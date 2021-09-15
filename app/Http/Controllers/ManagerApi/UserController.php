<?php
/**
 * File: UserController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\ManagerApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerApi\AttachUserRequest;
use App\Http\Requests\ManagerApi\StoreUserRequest;
use App\Http\Requests\ManagerApi\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private PersonRepository $personRepository;

    private UserRepository $userRepository;

    private UserService $userService;

    public function __construct(
        UserRepository $userRepository,
        PersonRepository $personRepository,
        UserService $userService
    ) {
        $this->userRepository = $userRepository;
        $this->personRepository = $personRepository;
        $this->userService = $userService;
    }

    /**
     * @param StoreUserRequest $request
     * @return UserResource
     */
    public function store(StoreUserRequest $request): UserResource
    {
        $userDto = $request->getUserDto();
        $personDto = $request->getPersonDto();

        $user = $this->userService->createUser($userDto, $personDto);
        $user->load('person');

        return new UserResource($user);
    }

    /**
     * @param AttachUserRequest $request
     * @return UserResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function createFromPerson(AttachUserRequest $request): UserResource
    {
        $dto = $request->getDto();
        $person = $this->personRepository->find($dto->person_id);
        $user = $this->userService->createUserForExistingPerson($person, $dto);
        $user->load('person');

        return new UserResource($user);
    }

    /**
     * @param string $id
     * @return UserResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function show(string $id): UserResource
    {
        $user = $this->userRepository->find($id);
        $user->load('person');

        return new UserResource($user);
    }

    /**
     * @param string $id
     * @param UpdateUserRequest $request
     * @return UserResource
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(string $id, UpdateUserRequest $request): UserResource
    {
        $user = $this->userRepository->find($id);
        $this->userService->update($user, $request->getDto());
        $user->load('person');

        return new UserResource($user);
    }

    /**
     * @param string $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $user = $this->userRepository->find($id);
        $this->userService->delete($user);
    }
}
