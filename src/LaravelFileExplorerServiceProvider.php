<?php

namespace Alireza\LaravelFileExplorer;


use Alireza\LaravelFileExplorer\Middleware\ValidateDisk;
use Illuminate\Contracts\Container\BindingResolutionException;
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
        //publish config
        $this->publishes([
            __DIR__ . '/../config/laravel-file-explorer.php' => config_path('laravel-file-explorer.php'),
        ], "laravel-file-explorer.config");

        //load api routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $router->aliasMiddleware('validate.disk', ValidateDisk::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void {}
}
