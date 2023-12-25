<?php

use Alireza\LaravelFileExplorer\Controllers\DiskController;
use Alireza\LaravelFileExplorer\Controllers\FileExplorerLoaderController;
use Alireza\LaravelFileExplorer\Services\ExplorerConfig;
use Illuminate\Support\Facades\Route;

$configService = resolve(ExplorerConfig::class);

Route::group([
    'middleware' => $configService->getMiddlewares(),
    'prefix'     => $configService->getRoutePrefix()
], function () {
    Route::get('load-file-explorer', [FileExplorerLoaderController::class, 'initFileExplorer']);
    Route::get('disks/{disk}', [DiskController::class, 'loadDiskDirs']);
    Route::get('disks/{diskName}/dirs/{dirName}', [FileExplorerLoaderController::class, 'loadDirItems']);
});
