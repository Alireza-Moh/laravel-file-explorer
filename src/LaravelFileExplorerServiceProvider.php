<?php

namespace AlirezaMoh\LaravelFileExplorer;

use AlirezaMoh\LaravelFileExplorer\Middleware\ValidateDisk;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelFileExplorerServiceProvider extends ServiceProvider
{
    public function boot(Router $router): void {
        //load middleware
        $router->aliasMiddleware('validate.disk', ValidateDisk::class);

        //publish config
        $this->publishConfig();

        //register api routes
        $this->registerApiRoutes();
    }

    public function register(): void {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-file-explorer.php',
            'laravel-file-explorer'
        );
    }

    private function publishConfig(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../config/laravel-file-explorer.php' => config_path('laravel-file-explorer.php'),
            ],
            'lfx.config'
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
