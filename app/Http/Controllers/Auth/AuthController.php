<?php
/**
 * File: AuthController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * Class LoginController
 * @package App\Http\Controllers\ManagerApi
 */
class AuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * AuthController constructor.
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function login(LoginRequest $request)
    {
        $user = $this->attemptLogin($request);

        if (null === $user) {
            return $this->sendFailedLoginResponse();
        }

        return $this->sendLoginResponse($user);
    }

    /**
     * Attempt to log the user into the application.
     * @param LoginRequest $request
     * @return User|null
     */
    protected function attemptLogin(LoginRequest $request): ?User
    {
        try {
            $user = $this->repository->findByUsername($request->username);
            if (\Hash::check($request->password, $user->password)) {
                return $user;
            }
        } catch (ModelNotFoundException $exception) {
            //
        }
        return null;
    }

    /**
     * Send the response after the user was authenticated.
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(User $user): \Illuminate\Http\JsonResponse
    {
        $this->clearLoginAttempts(\request());

        $token = $user->createToken($user->username);

        return \json_response($token);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(): \Symfony\Component\HttpFoundation\Response
    {
        abort(401, \trans('auth.failed'));
    }
}
