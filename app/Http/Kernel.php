<?php
declare(strict_types=1);

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Class Kernel
 * @package App\Http
 */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
        \Barryvdh\Cors\HandleCors::class,
    ];

    /**
     * The application's route middleware.
     * These middleware may be assigned to groups or used individually.
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
//        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
//        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
//        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'prefer_json' => \App\Http\Middleware\PreferJson::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,
        'cors' => \Barryvdh\Cors\HandleCors::class,
    ];

    /**
     * The priority-sorted list of middleware.
     * This forces non-global middleware to always be in the given order.
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
//        \App\Http\Middleware\Authenticate::class,
//        \Illuminate\Session\Middleware\AuthenticateSession::class,
//        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];

    /**
     * The application's route middleware groups.
     * @var array
     */
    protected $middlewareGroups = [
//        'web' => [
//            \App\Http\Middleware\EncryptCookies::class,
//            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
//            \Illuminate\Session\Middleware\StartSession::class,
//            \Illuminate\Session\Middleware\AuthenticateSession::class,
//            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
//            \App\Http\Middleware\VerifyCsrfToken::class,
//            \Illuminate\Routing\Middleware\SubstituteBindings::class,
//        ],

        'api' => [
            'prefer_json:1',
            'cors'
        ],

        'auth' => [
            'api',
        ],

        'public_api' => [
            'api',
        ],

        'manager_api' => [
            'api',
            'auth:api',
            'role:' . \App\Services\Permissions\UserRoles::ADMIN
            . '|' . \App\Services\Permissions\UserRoles::MANAGER
            . '|' . \App\Services\Permissions\UserRoles::OPERATOR
        ],

        'student_api' => [
            'api',
            'role:' . \App\Services\Permissions\UserRoles::STUDENT
        ],

        'customer_api' => [
            'api',
            'role:' . \App\Services\Permissions\UserRoles::CUSTOMER
        ],

        'instructor_api' => [
            'api',
            'role:' . \App\Services\Permissions\UserRoles::INSTRUCTOR
        ],
    ];
}
