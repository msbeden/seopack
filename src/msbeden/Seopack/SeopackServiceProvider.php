<?php

namespace msbeden\Seopack;

/**
 * Laravel 8 Seopack
 * @license MIT License
 * @author Mehmet Şaban BEDEN <msbeden@gmail.com>
 * @link https://www.msbeden.tk
 */

use Illuminate\Support\ServiceProvider;

class SeopackServiceProvider extends ServiceProvider
{

    /**
     * @var bool $defer Indicates if loading of the provider is deferred.
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        //the configuration file to be shared
        $this->publishes([
            __DIR__ . '/../../config/seopack.php' => config_path('seopack.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('seopack', function ($app) {
            return new Seopack($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['seopack'];
    }
}
