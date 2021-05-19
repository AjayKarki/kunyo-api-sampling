<?php

namespace Neputer\Providers;

use Foundation\Lib\Role;
use Illuminate\Support\Facades\Route;
use Foundation\Middleware\Impersonate;
use Neputer\Supports\Access\HasAccessMiddleware;

/**
 * Class RouteServiceProvider
 * @package Neputer\Providers
 */
class RouteServiceProvider extends \App\Providers\RouteServiceProvider
{

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        parent::mapApiRoutes();

        parent::mapWebRoutes();

        $this->mapAdminRoutes();

        $this->mapManagerRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        $accessMiddleware  = Role::getMiddlewareString();
        Route::middleware([ 'web', 'auth', Impersonate::class, $accessMiddleware, '2fa', ])
            ->namespace($this->namespace.'\Admin')
            ->prefix('spanel')
            ->as('admin.')
            ->group(base_path('routes/admin.php'));
    }

    protected function mapManagerRoutes()
    {
        $accessMiddleware  = 'access:' .Role::$current[Role::ROLE_SUPER_ADMIN].','.Role::$current[Role::ROLE_MANAGER];
        Route::middleware([ 'web', 'auth', $accessMiddleware, Impersonate::class,  ])
            ->namespace($this->namespace.'\Manager')
            ->prefix('spanel/manager')
            ->as('manager.')
            ->group(base_path('routes/manager.php'));
    }

}
