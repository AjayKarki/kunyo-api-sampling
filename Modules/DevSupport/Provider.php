<?php

namespace Modules\DevSupport;

use Illuminate\Support\ServiceProvider;

/**
 * Class Provider
 * @package Modules\DevSupport
 */
final class Provider extends ServiceProvider
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
            __DIR__ . '/config/support.php', 'support'
        );
        $this->app->register(\Modules\DevSupport\Http\Route\ServiceProvider::class, false);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/route.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'support');
    }

}
