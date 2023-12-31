<?php

namespace Detechtiva\VueCalendarForLaravel;

use Illuminate\Support\ServiceProvider;

class VueCalendarForLaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'vue-calendar-for-laravel');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'vue-calendar-for-laravel');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
//            $this->publishes([
//                __DIR__.'/../config/config.php' => config_path('vue-calendar-for-laravel.php'),
//            ], 'config');

            $this->publishes([
                __DIR__ . '/../database/migrations/create_events_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_events_table.php'),
                __DIR__ . '/../database/migrations/add_canceled_at_field_in_events_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_add_canceled_at_field_in_events_table.php'),
                __DIR__ . '/../database/migrations/add_parent_id_field_in_events_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_add_parent_id_field_in_events_table.php'),
                // you can add any number of migrations here
            ], 'migrations');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/vue-calendar-for-laravel'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/vue-calendar-for-laravel'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/vue-calendar-for-laravel'),
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
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'vue-calendar-for-laravel');

        // Register the main class to use with the facade
        $this->app->singleton('vue-calendar-for-laravel', function () {
            return new VueCalendarForLaravel;
        });
    }
}
