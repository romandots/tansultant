<?php
/**
 * File: LoginService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Login;

use App\Exceptions\Auth\UnauthorizedException;
use App\Http\Requests\Auth\DTO\Login;
use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class LoginService
 * @package App\Services\Login
 */
class LoginService
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * LoginService constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $username
     * @param string $password
     * @return \App\Models\User
     */
    private function attemptLogin(string $username, string $password): \App\Models\User
    {
        try {
            $user = $this->repository->findByUsername($username);
        } catch (ModelNotFoundException $exception) {
            throw new Exceptions\UserNotFoundException();
        }

        if (!\Hash::check($password, $user->password)) {
            throw new Exceptions\WrongPasswordException();
        }

        return $user;
    }

    /**
     * @param Login $login
     * @return \Laravel\Passport\PersonalAccessTokenResult
     */
    public function login(Login $login): \Laravel\Passport\PersonalAccessTokenResult
    {
        $user = $this->attemptLogin($login->username, $login->password);

        $this->repository->updateSeenAt($user);

        return $user->createToken($user->username);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool
    {
        return $user->token()->revoke();
    }
}
