<?php

namespace Neputer\Supports\Access;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Class Provider
 * @package Neputer\Supports\Access
 */
final class Provider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GeneratePermissionCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // @TODO need to make the path use on both here and on GeneratePermissionCommand
        $file = base_path('bootstrap') . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'permissions.php';
        if ($this->app['files']->exists($file)) {
            collect($this->app['files']->getRequire($file))->map(function ($permission) {
                Gate::define($permission, function($user) use ($permission){
                    return $user->havePermission($permission);
                });
            });
        }
    }

}
