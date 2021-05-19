<?php

namespace Neputer\Providers;

use Foundation\Services\KYCService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('admin.layouts.partials.right-sidebar', function ($view) {
            $view->with('kycVerificationRequests', app(KYCService::class)->countPendingRequests());
        });
    }
}
