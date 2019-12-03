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
use App\Http\Requests\ManagerApi\UpdateUserPasswordRequest;
use App\Http\Requests\ManagerApi\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 */
class UserController extends Controller
{
    /**
     * @var PersonRepository
     */
    private $personRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     * @param PersonRepository $personRepository
     * @param UserService $userService
     */
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
     * @param StoreUserRequest $request
     * @return UserResource
     */
    public function store(StoreUserRequest $request): UserResource
    {
        /** @var User $user */
        $user = DB::transaction(function () use ($request) {
            $person = $this->personRepository->create($request->getPersonDto());
            return $this->userRepository->create($person, $request->getUserDto());
        });
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
        $user = $this->userRepository->create($person, $dto);
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
        $this->userRepository->update($user, $request->getDto());
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

    /**
     * @param string $id
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function destroy(string $id): void
    {
        $user = $this->userRepository->find($id);
        $this->userRepository->delete($user);
    }
}
