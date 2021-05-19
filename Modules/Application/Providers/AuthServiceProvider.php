<?php

namespace Modules\Application\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as BaseAuthServiceProvider;

/**
 * Class AuthServiceProvider
 * @package Modules\Application\Providers
 */
final class AuthServiceProvider extends BaseAuthServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var
    array
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }

}
