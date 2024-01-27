<?php

namespace Alireza\LaravelFileExplorer\tests;

use Alireza\LaravelFileExplorer\LaravelFileExplorerServiceProvider;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;

class LaravelFileExplorerTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelFileExplorerServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterApplicationCreated(function () {
            Config::set([
                "laravel-file-explorer.route_prefix" => "api/laravel-file-explorer",
                "laravel-file-explorer.middlewares" => [],
                "laravel-file-explorer.disks" => ["tests", "web", "images"],
                "laravel-file-explorer.default_disk_on_loading" => "tests",
                "laravel-file-explorer.default_directory_from_default_disk_on_loading" => "ios",
                "laravel-file-explorer.allowed_file_extensions" => ["png", "jpg", "jpeg", "gif", "txt"],
            ]);

            Storage::fake("tests");
        });
    }
}
