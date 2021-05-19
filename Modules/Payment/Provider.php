<?php

namespace Modules\Payment;

use Illuminate\Support\ServiceProvider;

/**
 * Class Provider
 * @package Modules\Payment
 */
final class Provider extends ServiceProvider
{

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/gateway.php', 'gateway');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        \View::addLocation(__DIR__ . '/resources/views/');
    }

}
