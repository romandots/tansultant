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
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private LoginService $service;

    public function __construct(LoginService $service)
    {
        $this->service = $service;
    }

    /**
     * Log the user in to the application.
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $token = $this->service->login($request->getDto());

        return \json_response(['accessToken' => $token->plainTextToken]);
    }

    /**
     * Log the user out of the application.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws UnauthorizedException
     * @throws \Exception
     */
    public function logout(Request $request): void
    {
        $this->service->logout($request->user());
    }
}
