<?php

namespace Alireza\LaravelFileExplorer;

use Alireza\LaravelFileExplorer\Middleware\ValidateDisk;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LaravelFileExplorerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router): void {
        //load api routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        //load middleware
        $router->aliasMiddleware('validate.disk', ValidateDisk::class);

        //publish config
        $this->publishConfig();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-file-explorer.php',
            'laravel-file-explorer'
        );
    }

    /**
     * Publish package config file
     *
     * @return void
     */
    public function publishConfig(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../config/laravel-file-explorer.php' => config_path('laravel-file-explorer.php'),
            ],
            "lfx.config"
        );
    }
}
