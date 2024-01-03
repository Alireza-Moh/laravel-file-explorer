<?php

namespace Alireza\LaravelFileExplorer\Services;

final class ExplorerConfig
{
    /**
     * Get the default disk on loading.
     *
     * @return string|null The default disk name.
     */
    final public function getDefaultDiskOnLoading(): string|null
    {
        return config("laravel-file-explorer.default_disk_on_loading");
    }

    /**
     * Get the default directory on loading from the default disk.
     *
     * @return string|null The default directory path.
     */
    final public function getDefaultDirectoryOnLoading(): string|null
    {
        return config("laravel-file-explorer.default_directory_from_default_disk_on_loading");
    }

    /**
     * Get the list of configured disks.
     *
     * @return array The list of disk names.
     */
    final public function getDisks(): array
    {
        $disks = config("laravel-file-explorer.disks");

        if (empty($disks)) {
            return ["public"];
        }
        return $disks;
    }

    /**
     * Get the allowed file extensions.
     *
     * @return array|null The list of allowed file extensions.
     */
    final public function getAllowedFileExtensions(): array|null
    {
        return config("laravel-file-explorer.allowed_file_extensions");
    }

    /**
     * Get the maximum allowed file size.
     *
     * @return int|null The maximum allowed file size in bytes.
     */
    final public function getMaxAllowedFileSize(): int|null
    {
        return config("laravel-file-explorer.max_allowed_file_size");
    }

    /**
     * Get the configured middlewares.
     *
     * @return array|null The list of middleware names.
     */
    final public function getMiddlewares(): array|null
    {
        return config("laravel-file-explorer.middlewares");
    }

    /**
     * Get the route prefix.
     *
     * @return string|null The configured route prefix.
     */
    final public function getRoutePrefix(): string|null
    {
        return config("laravel-file-explorer.route_prefix");
    }
}
