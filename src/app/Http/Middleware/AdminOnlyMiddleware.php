<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnlyMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::guard('web')->user();


        if (!$user) {
            throw new AuthorizationException('Требуется аутентификация для доступа к этому ресурсу.');
        }

        if (!$user->isAdmin()) {
            throw new AuthorizationException('У вас нет прав для доступа к этому ресурсу.');
        }

        return $next($request);
    }
}
