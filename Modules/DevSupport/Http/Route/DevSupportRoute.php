<?php

namespace Modules\DevSupport\Http\Route;

use Foundation\Lib\Role;
use Neputer\Supports\Routing\RouteRegistrar;

/**
 * Class DevSupportRoute
 * @package Modules\DevSupport\Http\Route
 */
class DevSupportRoute extends RouteRegistrar
{

    public function map()
    {
        $accessMiddleware  = 'access:' .Role::$current[Role::ROLE_SUPER_ADMIN].','.Role::$current[Role::ROLE_MANAGER];

        $this->name('support::')
            ->middleware([ 'web' , 'auth', $accessMiddleware,])
            ->group(function () {
            $this->mapDevSupportRoutes();
        });
    }

    private function mapDevSupportRoutes()
    {
        $this->prefix('support')->name('support.')->group(function() {
            // support::support.dashboard
            $this->get('/dashboard', 'HomeAction')->name('home');
            $this->get('/information', 'InformationAction')->name('information');
            $this->get('/utility', 'UtilityAction')->name('utility');
            $this->group([ 'prefix' => 'log', ], function () {

                $this->get('/', 'LogAction')->name('log');
                $this->get('delete', 'Log\DeleteLogAction')->name('log.delete');

                $this->group([ 'prefix' => '{date}', 'namespace' => 'Log', ], function () {
                    $this->get('/', 'ShowLogAction')->name('log.show');
                    $this->get('download', 'DownloadLogAction')->name('log.download');
                    $this->get('{level}', 'FilterLevelLogAction')->name('log.filter');
                    $this->get('{level}/search', 'FilterLogAction')->name('log.search');
                });

            });
        });
    }

}
