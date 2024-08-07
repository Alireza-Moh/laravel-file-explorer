<?php

namespace AlirezaMoh\LaravelFileExplorer\Supports;

final class ConfigRepository
{
    final public static function getDefaultDiskOnLoading(): string
    {
        return config('laravel-file-explorer.default_disk_on_loading');
    }

    final public static function getDefaultDirectoryOnLoading(): string|null
    {
        return config('laravel-file-explorer.default_directory_on_loading');
    }

    final public static function getDisks(): array
    {
        return config('laravel-file-explorer.disks');
    }

    final public static function getAllowedFileExtensions(): array
    {
        return config('laravel-file-explorer.allowed_file_extensions');
    }

    final public static function getMaxAllowedFileSize(): int|null
    {
        return config('laravel-file-explorer.max_allowed_file_size');
    }

    final public static function getMiddlewares(): array
    {
        return config('laravel-file-explorer.middlewares');
    }

    final public static function getRoutePrefix(): string
    {
        return config('laravel-file-explorer.route_prefix');
    }

    final public static function hashFileWhenUploading(): bool
    {
        return config('laravel-file-explorer.hash_file_name_when_uploading');
    }

    final public static function getModifiedFileTimeFormat(): string
    {
        return config('laravel-file-explorer.modified_file_time_format');
    }

    final public static function getUserModel(): string
    {
        return config('laravel-file-explorer.user_model');
    }

    final public static function getRoles(): array
    {
        return config('laravel-file-explorer.roles');
    }

    final public static function isACLEnabled(): bool
    {
        return config('laravel-file-explorer.acl_enabled');
    }

    final public static function getPermissions(): array
    {
        return config('laravel-file-explorer.permissions');
    }
}
