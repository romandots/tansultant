<?php
/**
 * File: GetBearerTokenFromCookies.php
 * Author: Roman Dots <ram.d.kreiz@gmail.com>
 * Date: 2019-07-22
 * Copyright (c) 2019
 */

declare(strict_types=1);

namespace App\Http\Middleware;

/**
 * Class GetBearerTokenFromCookies
 * @package App\Http\Middleware
 *
 * This middleware gets bearer token from cookie and puts it to Authorization header
 */
class GetBearerTokenFromCookies
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $bearerToken = $request->header('Authorization')[0] ?? null;
        if (empty($bearerToken)) {
            $cookie = $request->cookie(\config('login.cookie'));
            if (!empty($cookie)) {
                $request->headers->set('Authorization', "Bearer {$cookie}");
            }
        }

        return $next($request);
    }
}
