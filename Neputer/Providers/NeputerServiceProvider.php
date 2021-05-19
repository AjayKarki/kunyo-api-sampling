<?php

namespace Neputer\Providers;

use App\Providers\ViewServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use Modules\Payment\Provider as PaymentServiceProvider;
use Neputer\Supports\Console\Commands\ClearCommand;
use Neputer\Supports\Access\GeneratePermissionCommand;
use Neputer\Supports\Theme\Provider as ThemeServiceProvider;
use Neputer\Supports\Console\Commands\GenerateJsRouteCommand;
use Neputer\Supports\Access\Provider as AccessServiceProvider;
use Neputer\Supports\Generator\Providers\GeneratorServiceProvider;

/**
 * Class NeputerServiceProvider
 *
 * @package Neputer\Providers
 * @version 0.1.3
 */
class NeputerServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        self::registerDirectives();
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->registerDeferredProvider(GeneratorServiceProvider::class);
        $this->app->registerDeferredProvider(AccessServiceProvider::class);
        $this->app->register(ThemeServiceProvider::class);
        $this->app->register(PaymentServiceProvider::class);
        if ($this->app->runningInConsole()) {
            $this->commands([
                ClearCommand::class,
                GenerateJsRouteCommand::class,
                GeneratePermissionCommand::class,
            ]);
        }

        self::addMacros();
    }

    /**
     * List of directive to be registered
     *
     * @return void
     */
    private function registerDirectives()
    {
        $directives = require base_path('/Neputer/Supports/directives.php');

        collect($directives)->each(function ($item, $key) {
            Blade::directive($key, $item);
        });
    }

    /**
     * List of macros to be registered
     *
     * @return void
     */
    private function addMacros()
    {
        Collection::make(glob(base_path('/Neputer/Supports/Macros/*.php')))
            ->mapWithKeys(function ($path) {
                return [$path => pathinfo($path, PATHINFO_FILENAME)];
            })
            ->each(function ($macro, $path) {
                require_once $path;
            });
    }

}
