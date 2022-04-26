<?php

namespace Kineticamobile\SmsAuth;

use Illuminate\Support\ServiceProvider;

class SmsAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/sms-auth.php' => config_path('sms-auth.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/../databases/migrations');

        if (config('sms-auth.enable')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
        }

        // Views
        $sourceViewsPath = __DIR__.'/../resources/views';
        $this->loadViewsFrom($sourceViewsPath, 'sms-auth');
        $this->publishes([
            $sourceViewsPath => resource_path('views/vendor/sms-auth'),
        ], 'views');
    }

  
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/sms-auth.php', 'sms-auth'
        );
    }
}
