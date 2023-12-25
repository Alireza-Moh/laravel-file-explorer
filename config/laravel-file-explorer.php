<?php

return [

    /**
     * Default disk to load on initialization.
     *
     * Type: string
     */
    "default_disk_on_loading" => "public",

    /**
     * Default directory from the default disk to load on initialization.
     *
     * if null it will auto select the first directory from the list
     *
     * Type: string | null
     */
    "default_directory_from_default_disk_on_loading" => null,

    /**
     * List of available disks. Specify disk names from config/filesystems.
     *
     * Type: array
     */
    "disk_list" => ["public"],

    /**
     * Allowed file extensions for uploads. Set to null for no restrictions.
     *
     * Type: string | null
     */
    "allowed_file_extensions" => null,

    /**
     * Maximum allowed file size for uploads. Set to null for no restrictions.
     *
     * Type: int | null
     */
    "max_allowed_file_size" => null,

    /**
     * Middlewares applied to the file explorer. For example, ['web', 'auth'].
     *
     * Type: array | null
     */
    "middlewares" => ["web"],

    /**
     * Route prefix for the file explorer.
     *
     * Type: string
     */
    "route_prefix" => "laravel-file-explorer"
];
