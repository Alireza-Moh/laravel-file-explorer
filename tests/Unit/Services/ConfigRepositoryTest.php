<?php

use AlirezaMoh\LaravelFileExplorer\Services\ConfigRepository;
use Illuminate\Support\Facades\Config;

test('returns the default disk on loading', function () {
    Config::set('laravel-file-explorer.default_disk_on_loading', 'tests');
    $result = ConfigRepository::getDefaultDiskOnLoading();

    expect($result)->toBe('tests');
});

test('returns the default directory on loading from the default disk', function () {
    Config::set('laravel-file-explorer.default_directory_on_loading', 'ios');

    $result = ConfigRepository::getDefaultDirectoryOnLoading();

    expect($result)->toBe('ios');
});

test('returns the list of configured disks', function () {
    Config::set("laravel-file-explorer.disks", ["mobile", "tests"]);

    $result = ConfigRepository::getDisks();

    expect($result)->toMatchArray(['mobile', 'tests']);
});

test('returns the allowed file extensions', function () {
    Config::set("laravel-file-explorer.allowed_file_extensions", ['jpg', 'png']);

    $result = ConfigRepository::getAllowedFileExtensions();

    expect($result)->toMatchArray(['jpg', 'png']);
});

test('returns the maximum allowed file size', function () {
    Config::set("laravel-file-explorer.max_allowed_file_size", 1024);

    $result = ConfigRepository::getMaxAllowedFileSize();

    expect($result)->toBeInt(1024);
});

test('returns the configured middlewares', function () {
    Config::set("laravel-file-explorer.middlewares", ['auth', 'admin']);

    $result = ConfigRepository::getMiddlewares();

    expect($result)->toMatchArray(['auth', 'admin']);
});

test('returns the route prefix', function () {
    Config::set("laravel-file-explorer.route_prefix", "file-explorer");

    $result = ConfigRepository::getRoutePrefix();

    expect($result)->toBe('file-explorer');
});

test('returns whether to hash file names when uploading', function () {
    Config::set("laravel-file-explorer.hash_file_name_when_uploading", true);

    $result = ConfigRepository::getHashFileWhenUploading();

    expect($result)->toBeTrue();
});
