<?php

namespace App\Common;

class Route extends \Illuminate\Support\Facades\Route
{
    public static function namedGroup(string $prefix, string $controllerClass, \Closure $closure)
    {
        return \Illuminate\Support\Facades\Route::name($prefix . '.')
            ->prefix($prefix)
            ->controller($controllerClass)
            ->group($closure);
    }

    public static function namedRoute(string $action, string $method, string $path, array $permissions)
    {
        $route = match(strtolower($method)) {
            'get' => \Illuminate\Support\Facades\Route::get($path, $action),
            'post' => \Illuminate\Support\Facades\Route::post($path, $action),
            'put' => \Illuminate\Support\Facades\Route::put($path, $action),
            'patch' => \Illuminate\Support\Facades\Route::patch($path, $action),
            'delete' => \Illuminate\Support\Facades\Route::delete($path, $action),
            default => throw new \Exception('To be implemented'),
        };

        $guardName = \config('permission.guard_name', 'api');
        $middleware = sprintf('permission:%s,%s', implode('|', $permissions), $guardName);

        return $route->name($action)->middleware($middleware);
    }
}
