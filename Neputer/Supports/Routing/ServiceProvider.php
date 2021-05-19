<?php

namespace Neputer\Supports\Routing;

use Modules\DevSupport\Http\Route\DevSupportRoute;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

/**
 * Class ServiceProvider
 * @package Neputer\Supports\Routing
 */
class ServiceProvider extends RouteServiceProvider
{

    const CONFIG_ROUTE_ATTRS_KEY = 'neputer';

    /**
     * Get Route attributes
     *
     * @return array
     */
    public function routeAttributes()
    {
        return array_merge($this->config(static::CONFIG_ROUTE_ATTRS_KEY, []), []);
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->group($this->routeAttributes(), function() {
            static::init();
        });
    }

    /**
     * Get config value by key
     *
     * @param  string      $key
     * @param  mixed|null  $default
     *
     * @return mixed
     */
    private function config(string $key, $default = null)
    {
        return $this->app['config']->get($key, $default);
    }

}
