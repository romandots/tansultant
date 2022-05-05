<?php
/**
 * File: LoginService.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Services\Login;

use App\Common\BaseService;
use App\Components\Loader;
use App\Http\Requests\Auth\DTO\Login;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class LoginService
 * @package App\Services\Login
 */
class LoginService extends BaseService
{
    private \App\Components\User\Facade $users;

    public function __construct()
    {
        $this->users = Loader::users();
    }

    /**
     * @param string $username
     * @param string $password
     * @return User
     */
    private function attemptLogin(string $username, string $password): User
    {
        try {
            $user = $this->users->findByUsername($username);
        } catch (ModelNotFoundException $exception) {
            throw new Exceptions\UserNotFoundException();
        }

        if (!\Hash::check($password, $user->password)) {
            throw new Exceptions\WrongPasswordException();
        }

        return $user;
    }

    public function login(Login $login): \Laravel\Sanctum\NewAccessToken
    {
        $user = $this->attemptLogin($login->username, $login->password);

        $this->users->updateSeenAt($user);

        return $user->createToken($user->username);
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function logout(User $user): bool
    {
        $token = $user->token();

        if (null !== $token) {
            return false;
        }

        $token->revoke();

        return true;
    }
}
