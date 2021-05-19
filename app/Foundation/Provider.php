<?php

namespace Foundation;

use Foundation\Lib\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Sms\Provider as SmsServiceProvider;
use Foundation\Composers\PrimaryMenuViewComposer;
use Modules\Email\Provider as EmailServiceProvider;
use Modules\Application\Provider as ApiServiceProvider;
use Foundation\Composers\FooterPrimaryMenuViewComposer;
use Modules\Payment\Provider as PaymentServiceProvider;
use Modules\DevSupport\Provider as DevSupportServiceProvider;
use Foundation\Commands\Reports\UpdateOrderReportsViewCommand;
use Foundation\Commands\Reports\UpdateProductNamesOnTransaction;

/**
 * Class Provider
 * @package Foundation
 */
final class Provider extends ServiceProvider
{

    /**
     * Sharing the data to the views
     *
     * key as view path & value as the view composer
     *
     * @var array
     */
    private static $shareable = [
        'pages.partials.primary-menu' => PrimaryMenuViewComposer::class,
        'pages.partials.footer-primary-menu' => FooterPrimaryMenuViewComposer::class,
//        '*' => SettingViewComposer::class,
//        'layouts.partials.header' => SettingViewComposer::class,
//        'layouts.partials.footer' => SettingViewComposer::class,
    ];

    /**
     * Aliasing the component
     *
     * key as view path & value as the alias
     *
     * @var array
     */
    private static $components = [
        'admin.common.components.summary'  => 'summary',
        'admin.common.breadcrumbs'         => 'breadcrumb',
        'admin.common.advanced-filter'     => 'filter',
        'admin.common.summary-script'      => 'summaryscripts',
        'pages.partials.primary-menu'      => 'primary',
        'pages.partials.footer-primary-menu'      => 'fprimary',
        'admin.common.components.box'      => 'box',
        'admin.common.components.piechart' => 'piechart',
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (Provider::$components as $view => $alias ) {
            \Blade::aliasComponent($view, $alias);
        }

        foreach (Provider::$shareable as $view => $composer ) {
            \View::composer( $view, $composer );
        }

        $this->mergeConfigFrom(__DIR__.'/Config/setting.php', 'setting');
        $this->mergeConfigFrom(__DIR__.'/Config/Notifier/notifier.php', 'notifier');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(PaymentServiceProvider::class);
        $this->app->register(SmsServiceProvider::class);
        $this->app->register(EmailServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->app->register(DevSupportServiceProvider::class);
        $this->app->register(ApiServiceProvider::class);

        $this->commands([
            UpdateOrderReportsViewCommand::class,
            UpdateProductNamesOnTransaction::class,
        ]);
    }

}
