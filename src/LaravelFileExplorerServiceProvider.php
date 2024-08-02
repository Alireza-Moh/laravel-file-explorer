<?php

namespace AlirezaMoh\LaravelFileExplorer;

use AlirezaMoh\LaravelFileExplorer\Http\Middleware\ValidatePermission;
use AlirezaMoh\LaravelFileExplorer\Http\Middleware\ValidateDisk;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Console\View\Components\Factory;

class LaravelFileExplorerServiceProvider extends ServiceProvider
{
    private const MIGRATION_FILE_NAME_SLUG = 'laravel_file_explorer_';

    public function boot(Router $router): void {
        //load middleware
        $router->aliasMiddleware('validateDisk', ValidateDisk::class);
        $router->aliasMiddleware('checkPermission', ValidatePermission::class);

        //publish config
        $this->publishConfig();

        //register api routes
        $this->registerApiRoutes();

        //publish migrations
        $this->publishMigrations();
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

    public function publishMigrations(): void
    {
        if ($this->isMigrationPublished()) {
            $component = new Factory(new ConsoleOutput());
            $component->warn("Migration files already published");
        }
        else {
            $this->publishes(
                [
                    __DIR__ . '/../database/migrations/' => database_path('migrations')
                ],
                'lfx.migrations'
            );
        }
    }

    private function isMigrationPublished(): bool
    {
        $files = File::files(database_path('migrations'));

        $migrationFiles = collect($files)->filter(function ($file) {
            return str_contains($file->getFilename(), self::MIGRATION_FILE_NAME_SLUG);
        });

        return $migrationFiles->isNotEmpty() && $migrationFiles->count() === 2;
    }

}
