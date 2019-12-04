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
     * @return \App\Models\User|null
     */
    public function attemptLogin(string $username, string $password): ?\App\Models\User
    {
        try {
            $user = $this->repository->findByUsername($username);
            if (\Hash::check($password, $user->password)) {
                return $user;
            }
        } catch (ModelNotFoundException $exception) {
            //
        }
        return null;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool
    {
        return $user->token()->revoke();
    }

    public function getUserByToken(string $accessToken): User
    {
        return $this->repository->findByAccessToken($accessToken);
    }
}
