<?php

namespace Modules\Application\Providers;

use Modules\Application\Http\Route\ApplicationRoute;
use Neputer\Supports\Routing\ServiceProvider as BaseRouteServiceProvider;

/**
 * Class ServiceProvider
 *
 * @package Modules\Application\Providers
 */
final class RouteServiceProvider extends BaseRouteServiceProvider
{

    const CONFIG_ROUTE_ATTRS_KEY = 'application.routes';

    /**
     * Define the array of the routes for the modules
     *
     * @return array
     */
    public static function init(): array
    {
        return [
            ApplicationRoute::register(),
        ];
    }

}
