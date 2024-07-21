<?php

return [


    /*
    |--------------------------------------------------------------------------
    | List of the disks
    |--------------------------------------------------------------------------
    |
    | You may specify the list of available disks by providing
    | their names from config/filesystems.
    |
    | Type: array
    */
    "disks" => ["public"],

    /*
    |--------------------------------------------------------------------------
    | Default disk
    |--------------------------------------------------------------------------
    |
    | You may set the default disk that the file explorer will load
    | on initialization.
    |
    | Type: string
    */
    "default_disk_on_loading" => "public",

    /*
    |--------------------------------------------------------------------------
    | Default directory
    |--------------------------------------------------------------------------
    |
    | You may set the default directory from the default disk to load
    | on initialization.
    | If null it will auto select the first directory from the list.
    |
    | Type: string | null
    */
    "default_directory_on_loading" => null,

    /*
    |--------------------------------------------------------------------------
    | File extensions
    |--------------------------------------------------------------------------
    |
    | You may specify the allowed file extensions for uploads.
    | Set to null for no restrictions.
    |
    | Type: array
    */
    "allowed_file_extensions" => ['json', 'txt'],

    /*
    |--------------------------------------------------------------------------
    | File size
    |--------------------------------------------------------------------------
    |
    | You may set the maximum allowed file size for uploads.
    | Set to null for no restrictions.
    |
    | Type: int | null
    | null => no restrictions
    */
    "max_allowed_file_size" => null,

    /*
    |--------------------------------------------------------------------------
    | Middlewares
    |--------------------------------------------------------------------------
    |
    | You may specify middlewares applied to the file explorer
    | for example, ['web', 'auth'].
    |
    | Type: array
    */
    "middlewares" => ["web"],

    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    |
    | Route prefix for the file explorer. You may set
    | the route prefix for the file explorer.
    |
    | Type: string
    */
    "route_prefix" => "api/laravel-file-explorer",

    /*
    |--------------------------------------------------------------------------
    | Filename hashing
    |--------------------------------------------------------------------------
    |
    | You may want to hash your filenames when uploading new files.
    |
    | Type: boolean
    */
    "hash_file_name_when_uploading" => true,
];
