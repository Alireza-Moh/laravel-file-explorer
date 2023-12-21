<?php

return [
    "default_disk_on_loading" => "public",
    "default_directory_from_default_disk_on_loading" => null,
    "disk_list" => ["public"],
    "allowed_file_extensions" => ["gif", "jpeg", "jpg", "json", "pdf", "png", "svg", "txt", "xml", "webp"],
    "max_allowed_file_size" => null,
    "middlewares" => ["web"],
    "route_prefix" => "laravel-file-explorer"
];
