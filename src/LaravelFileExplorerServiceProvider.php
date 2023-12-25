<?php

namespace Alireza\LaravelFileExplorer;


use Illuminate\Support\ServiceProvider;

class LaravelFileExplorerServiceProvider extends ServiceProvider
{
    public function boot(): void {
        //publish config
        $this->publishes([
            __DIR__ . '/../config/laravel-file-explorer.php' => config_path('laravel-file-explorer.php'),
        ], "laravel-file-explorer.config");

        //load api routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    public function register() {}
}
