<?php

namespace Neputer\Supports\Routing;

/**
 * Class RouteRegistrar
 *
 * @package Neputer\Supports\Routing
 */
abstract class RouteRegistrar
{

    /**
     * Register and map routes.
     */
    public static function register()
    {
        (new static)->map();
    }

    /**
     * Map the routes for the application.
     */
    abstract public function map();

    /**
     * Call the router method.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return app('router')->$name(...$arguments);
    }

}
