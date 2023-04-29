<?php
/**
 * File: UserController.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-20
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Components\Loader;
use App\Components\User\Formatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManagerApi\UpdateUserPasswordRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(Request $request): Formatter
    {
        $user = $request->user();
        $user->load('person', 'roles', 'permissions');

        return new Formatter($user);
    }

    public function updatePassword(UpdateUserPasswordRequest $request): void
    {
        $user = $request->user();
        $dto = $request->getDto();
        Loader::users()->updatePassword($user, $dto);
    }
}
