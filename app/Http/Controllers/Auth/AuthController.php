<?php
/**
 * File: AuthController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-12-4
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Exceptions\Auth\UnauthorizedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Login\LoginService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
        $token = $this->service->login($request->getDto());

        return \json_response($token);
    }

    /**
     * Log the user out of the application.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UnauthorizedException
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->service->logout($request->user());

        return \json_response('OK', 200);
    }
}
