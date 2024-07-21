<?php

namespace AlirezaMoh\LaravelFileExplorer\Supports;

final class ConfigRepository
{
    /**
     * Get the default disk on loading
     *
     * @return string
     */
    final public static function getDefaultDiskOnLoading(): string
    {
        return config("laravel-file-explorer.default_disk_on_loading");
    }

    /**
     * Get the default directory on loading from the default disk
     *
     * @return string|null
     */
    final public static function getDefaultDirectoryOnLoading(): string|null
    {
        return config("laravel-file-explorer.default_directory_on_loading");
    }

    /**
     * Get the list of configured disks
     *
     * @return array
     */
    final public static function getDisks(): array
    {
        return config("laravel-file-explorer.disks");
    }

    /**
     * Get the allowed file extensions
     *
     * @return array
     */
    final public static function getAllowedFileExtensions(): array
    {
        return config("laravel-file-explorer.allowed_file_extensions");
    }

    /**
     * Get the maximum allowed file size
     *
     * @return int|null
     */
    final public static function getMaxAllowedFileSize(): int|null
    {
        return config("laravel-file-explorer.max_allowed_file_size");
    }

    /**
     * Get the configured middlewares
     *
     * @return array
     */
    final public static function getMiddlewares(): array
    {
        return config("laravel-file-explorer.middlewares");
    }

    /**
     * Get the route prefix.
     *
     * @return string
     */
    final public static function getRoutePrefix(): string
    {
        return config("laravel-file-explorer.route_prefix");
    }

    /**
     * Should hash file name when uploading new items
     *
     * @return bool
     */
    final public static function getHashFileWhenUploading(): bool
    {
        return config("laravel-file-explorer.hash_file_name_when_uploading");
    }
}
