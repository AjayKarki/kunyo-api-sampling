<?php

namespace Modules\Application\Http\Route;

use Neputer\Supports\Routing\RouteRegistrar;

/**
 * Class ApplicationRoute
 * @package Modules\Application\Http\Route
 */
class ApplicationRoute extends RouteRegistrar
{

    public function map()
    {
        $this->name('api.')
            ->group(function () {

                $this->get('/login', 'Auth\LoginAction')->name('login');

            });
    }

}
