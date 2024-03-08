<?php

namespace AlirezaMoh\LaravelFileExplorer;

use AlirezaMoh\LaravelFileExplorer\Middleware\ValidateDisk;
use AlirezaMoh\LaravelFileExplorer\Services\ConfigRepository;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LaravelFileExplorerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router): void {
        //load middleware
        $router->aliasMiddleware('validate.disk', ValidateDisk::class);

        //publish config
        $this->publishConfig();

        //register api routes
        $this->registerApiRoutes();
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
    private function publishConfig(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../config/laravel-file-explorer.php' => config_path('laravel-file-explorer.php'),
            ],
            "lfx.config"
        );
    }

    private function registerApiRoutes(): void
    {
        Route::group($this->getRouteConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    private function getRouteConfiguration(): array
    {
        return [
            'prefix' => ConfigRepository::getRoutePrefix(),
            'middleware' => ConfigRepository::getMiddlewares(),
        ];
    }
}
