<?php

namespace Modules\Email;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/email.php', 'email');
        \View::addLocation(__DIR__ . '/resources/views/');
    }

}
