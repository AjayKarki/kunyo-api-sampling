<?php

namespace Modules\DevSupport\Http\Route;

use Neputer\Supports\Routing\ServiceProvider as RouteServiceProvider;

/**
 * Class ServiceProvider
 * @package Modules\DevSupport\Http\Route
 */
final class ServiceProvider extends RouteServiceProvider
{

    const CONFIG_ROUTE_ATTRS_KEY = 'support.routes';


    /**
     * Define the array of the routes for the modules
     *
     * @return array
     */
    public static function init(): array
    {
        return [
            DevSupportRoute::register(),
        ];
    }

}
