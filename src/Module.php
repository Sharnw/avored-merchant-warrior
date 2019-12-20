<?php
namespace Sharnw\AvoRedMerchantWarrior;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use AvoRed\Framework\Support\Facades\Payment;
use AvoRed\Framework\Support\Facades\Tab;
use Sharnw\AvoRedMerchantWarrior\API\Request;
use AvoRed\Framework\Tab\TabItem;

class Module extends ServiceProvider implements DeferrableProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerConfigData();
        $this->registerResources();
        $this->registerPaymentOption();
        $this->registerTab();
        $this->publishFiles();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('mwarr', function ($app) {
            return new Request($this->app['config']->get('mwarr'));
        });

        $this->app->alias('mwarr', Request::class);
    }

    public function registerConfigData()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mwarr.php', 'mwarr');
    }

    /**
     * Registering AvoRed Hello World Resource
     * e.g. Route, View, Database  & Translation Path
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'mw-card');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mw-card');
    }

    /**
     * Register Payment Option for App.
     *
     * @return void
     */
    protected function registerPaymentOption()
    {
        $payment = new CardPayment();
        Payment::put($payment);
    }

    /**
     * Register Menu Tab.
     *
     * @return void
     */
    public function registerTab()
    {
        Tab::put('system.configuration', function (TabItem $tab) {
            $tab->key('system.configuration.merchant-warrior')
                ->label('mw-card::merchant-warrior.config-title')
                ->view('mw-card::system.configuration.payment-card');
        });
    }

    /**
     * Publish Files for this module.
     * @return void
     */
    public function publishFiles()
    {
        $this->publishes([
            __DIR__ . '/../dist/js' => public_path('avored-admin/js'),
        ], 'sharnw-merchant-warrior');
    }

}