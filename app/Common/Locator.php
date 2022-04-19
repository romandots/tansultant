<?php

namespace App\Common;

abstract class Locator
{
    public static function facade(string $class) {
        $container = app();
        if ($container->has($class)) {
            return $container->get($class);
        }
        $instance = app()->make($class);
        $container->instance($class, $instance);
        return $instance;
    }
}