<?php

namespace Neputer\Supports\Theme;

use Illuminate\Support\ServiceProvider;
use Neputer\Supports\Theme\Engines\ThemeScanner;
use Neputer\Supports\Theme\Console\EditThemeCommand;
use Neputer\Supports\Theme\Console\ListThemeCommand;
use Neputer\Supports\Theme\Console\CreateThemeCommand;
use Neputer\Supports\Theme\Console\DestroyThemeCommand;

/**
 * Class Provider
 * @package Neputer\Supports\Theme
 */
class Provider extends ServiceProvider
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
            __DIR__.'/config/theme.php', 'theme'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateThemeCommand::class,
                ListThemeCommand::class,
                EditThemeCommand::class,
                DestroyThemeCommand::class,
            ]);
        }

        $this->app->singleton('theme', function ($app) {
            return new Theme(
                new ThemeScanner
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /* Register Routes */
        $routePath = theme_path($this->app['theme']->active()) . DIRECTORY_SEPARATOR .'route.php';
        if ($this->app['files']->exists($routePath)) {
            $this->loadRoutesFrom($routePath);
        }

        /* Register Languages */
        $lang = theme_path($this->app['theme']->active()) . DIRECTORY_SEPARATOR . $this->app['config']->get('theme.directories.lang', 'lang');
        $this->loadTranslationsFrom($lang, $this->app['theme']->active());
    }

}
