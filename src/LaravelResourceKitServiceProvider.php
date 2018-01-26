<?php

namespace Dododedodonl\LaravelResourceKit;

use Illuminate\Support\ServiceProvider;

class LaravelResourceKitServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'ResourceKit');

        $this->publishes([
            __DIR__ . '/views' => base_path('resources/views/vendor/ResourceKit'),
            __DIR__.'/config/resourcekit.php' => config_path('resourcekit.php'),
        ], 'resourcekit');

        $this->publishes([
            __DIR__ . '/views' => base_path('resources/views/vendor/ResourceKit')
        ], 'resourcekit.views');

        $this->publishes([
            __DIR__.'/config/resourcekit.php' => config_path('resourcekit.php'),
        ], 'resourcekit.config');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/resourcekit.php', 'resourcekit'
        );
    }
}
