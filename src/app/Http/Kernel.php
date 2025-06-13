<?php
declare(strict_types=1);

namespace App\Http;

use App\Http\Middleware\AdminOnlyMiddleware;
use App\Http\Middleware\AuthApiKeyHeader;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\PreferJson;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\UserHasPerson;
use App\Services\Permissions\UserRoles;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request.
     *
     * @var array<int, class-string>
     */
    protected $middleware = [
        HandleCors::class,
        //\App\Http\Middleware\CheckForMaintenanceMode::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's middleware groups.
     *
     * @var array<string, array<int, string>>
     */
    protected $middlewareGroups = [
        'web' => [
            'auth.basic:web,username',
            'admin_only',
        ],

        'webhooks' => [
            'auth.api_key',
        ],

        'api' => [
            'prefer_json:1',
        ],

        'member_api' => [
            'api',
            'auth:sanctum',
        ],

        'manager_api' => [
            'member_api',
            'role:' . UserRoles::ADMIN . '|' . UserRoles::MANAGER . '|' . UserRoles::OPERATOR
        ],
    ];

    /**
     * The application's route middleware.
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string>
     */
    protected $routeMiddleware = [
        // Authentication
        'auth' => Authenticate::class,
        'auth.basic' => AuthenticateWithBasicAuth::class,
        'auth.api_key' => AuthApiKeyHeader::class,
        'guest' => RedirectIfAuthenticated::class,

        // Authorization
        'admin_only' => AdminOnlyMiddleware::class,
        'can' => Authorize::class,
        'permission' => PermissionMiddleware::class,
        'role' => RoleMiddleware::class,
        'role_or_permission' => RoleOrPermissionMiddleware::class,
        'user_has_person' => UserHasPerson::class,

        // HTTP features
        'cache.headers' => SetCacheHeaders::class,
        'signed' => ValidateSignature::class,
        'throttle' => ThrottleRequests::class,
        'prefer_json' => PreferJson::class,

        // Disabled middleware
//        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
//        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

    /**
     * The priority-sorted list of middleware.
     * This forces non-global middleware to always be in the given order.
     *
     * @var array<int, class-string>
     */
    protected $middlewarePriority = [
        StartSession::class,
        ShareErrorsFromSession::class,
        Authenticate::class,
//        \Illuminate\Session\Middleware\AuthenticateSession::class,
//        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        Authorize::class,
    ];
}
