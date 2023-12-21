<?php

namespace Alireza\LaravelFileExplorer;

use Illuminate\Support\ServiceProvider;

class LaravelFileExplorerServiceProvider extends ServiceProvider
{
    public function boot(): void {
        //publish config
        $this->publishes([
            __DIR__.'/config/laravel-file-explorer.php' => config_path('laravel-file-explorer.php'),
        ]);

        //publish routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }

    public function register() {}
}
