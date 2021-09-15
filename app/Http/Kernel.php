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
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
//        \Spatie\Cors\Cors::class,
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
        'user_has_person' => \App\Http\Middleware\UserHasPerson::class,
    ];

    /**
     * The priority-sorted list of middleware.
     * This forcphp artisan vendor:publish --tag="cors"es non-global middleware to always be in the given order.
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
        'api' => [
            'prefer_json:1',
        ],

        'member_api' => [
            'api',
            'auth:api',
        ],

        'manager_api' => [
            'member_api',
            'role:' . \App\Services\Permissions\UserRoles::ADMIN
            . '|' . \App\Services\Permissions\UserRoles::MANAGER
            . '|' . \App\Services\Permissions\UserRoles::OPERATOR
        ],

        'student_api' => [
            'member_api',
            'role:' . \App\Services\Permissions\UserRoles::STUDENT
        ],

        'customer_api' => [
            'member_api',
            'role:' . \App\Services\Permissions\UserRoles::CUSTOMER
        ],

        'instructor_api' => [
            'member_api',
            'role:' . \App\Services\Permissions\UserRoles::INSTRUCTOR
        ],
    ];
}
