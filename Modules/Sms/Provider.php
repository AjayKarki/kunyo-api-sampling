<?php

namespace Modules\Sms;

use Illuminate\Support\ServiceProvider;

/**
 * Class Provider
 * @package Modules\Sms
 */
final class Provider extends ServiceProvider
{

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/sms.php', 'sms');
        \View::addLocation(__DIR__ . '/resources/views/');
    }

}
