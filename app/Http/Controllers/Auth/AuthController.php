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
use App\Services\Login\LoginService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Exceptions\Auth\UnauthorizedException;

/**
 * Class LoginController
 * @package App\Http\Controllers\ManagerApi
 */
class AuthController extends Controller
{
    use AuthenticatesUsers;

    /**
     * @var LoginService
     */
    private $service;

    /**
     * AuthController constructor.
     * @param LoginService $service
     */
    public function __construct(LoginService $service)
    {
        $this->service = $service;
    }

    /**
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response|
     */
    public function login(LoginRequest $request)
    {
        $user = $this->service->attemptLogin($request->username, $request->password);

        if (null === $user) {
            return $this->sendFailedLoginResponse();
        }

        return $this->sendLoginResponse($user);
    }

    /**
     * Log the user out of the application.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UnauthorizedException
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->middleware('auth:api');
        $this->service->logout($request->user());

        return \json_response('OK', 200);
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
