<?php

namespace Alireza\LaravelFileExplorer\Services;

final class ExplorerConfig
{
    final public function getDefaultDiskOnLoading(): string|null
    {
        return config("laravel-file-explorer.default_disk_on_loading");
    }

    final public function getDefaultDirectoryOnLoading(): string|null
    {
        return config("laravel-file-explorer.default_directory_from_default_disk_on_loading");
    }

    final public function getDisks(): array
    {
        $disks = config("laravel-file-explorer.disks");

        if (empty($disks)) {
            return ["public"];
        }
        return $disks;
    }

    final public function getAllowedFileExtensions(): array|null
    {
        return config("laravel-file-explorer.allowed_file_extensions");
    }

    final public function getMaxAllowedFileSize(): int|null
    {
        return config("laravel-file-explorer.max_allowed_file_size");
    }

    final public function getMiddlewares(): array|null
    {
        return config("laravel-file-explorer.middlewares");
    }

    final public function getRoutePrefix(): string|null
    {
        return config("laravel-file-explorer.route_prefix");
    }
}
