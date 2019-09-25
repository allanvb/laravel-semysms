<?php

namespace Allanvb\LaravelSemysms;

use Illuminate\Support\ServiceProvider;

class SemySmsServiceProvider extends ServiceProvider
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
        $this->app->bind('semy-sms', Client::class);
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/semy-sms.php', 'semy-sms');
        $this->publishes([__DIR__ . '/../config/semy-sms.php' => config_path('semy-sms.php')], 'config');
        require __DIR__.'/routes.php';
    }
}
