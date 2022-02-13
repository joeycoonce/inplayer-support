<?php

namespace SocialPiranha\InPlayerSupport;

use SocialPiranha\InPlayerSupport\Services\InPlayerService;
use Illuminate\Support\ServiceProvider;

class InPlayerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'inplayer-support');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'inplayer-support');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            // $this->publishes([
            //     __DIR__.'/../config/services.php' => config_path('services.php'),
            // ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/inplayer-support'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/inplayer-support'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/inplayer-support'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/services.php', 'services');

        // Register the main class to use with the facade
        $this->app->singleton('inplayer', function () {
            return new InPlayer;
        });
        $this->app->singleton(
            abstract: InPlayerService::class,
            concrete: fn() => new InPlayerService(
                url: strval(config(key: 'services.inplayer.url')),
                client_id: strval(config(key: 'services.inplayer.client_id')),
                client_secret: strval(config(key: 'services.inplayer.client_secret')),
                merchant_uuid: strval(config(key: 'services.inplayer.merchant_uuid')),
                merchant_password: strval(config(key: 'services.inplayer.merchant_password')),
            ),
        );
    }
}
