<?php

declare(strict_types=1);

namespace App\Http\Middleware;

class AuthApiKeyHeader
{
    public function handle($request, \Closure $next)
    {
        $headerApiKey = $request->header('x-api-key');
        $validApiKey = env('TANSULTANT_API_KEY');
        if ($validApiKey === $headerApiKey) {
            return $next($request);
        }

        throw new \App\Exceptions\Auth\UnauthorizedException();
    }
}
