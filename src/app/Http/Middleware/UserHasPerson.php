<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exceptions\UserHasNoPersonException;
use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class UserHasPerson
{
    public function handle($request, Closure $next)
    {
        if (Auth::guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        if (null === $request->user()->person) {
            throw new UserHasNoPersonException();
        }

        return $next($request);
    }
}
