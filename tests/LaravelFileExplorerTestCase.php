<?php

namespace Alireza\LaravelFileExplorer\Tests;

use Alireza\LaravelFileExplorer\LaravelFileExplorerServiceProvider;
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
                "laravel-file-explorer.default_directory_on_loading" => "ios",
                "laravel-file-explorer.allowed_file_extensions" => ["png", "jpg", "jpeg", "gif", "txt"],
                "laravel-file-explorer.hash_file_name_when_uploading" => false
            ]);

            Storage::fake("tests");
        });
    }
}
