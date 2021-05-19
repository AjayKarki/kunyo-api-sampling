<?php

namespace Modules\Application;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Modules\Application\Providers\AuthServiceProvider;
use Modules\Application\Providers\RouteServiceProvider;

final class Provider  extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/application.php', 'application'
        );
//        $this->app->register(RouteServiceProvider::class, false);
        $this->app->register(AuthServiceProvider::class, false);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/route.php');

//        Router::macro('productType', function ());
    }

}
