<?php

use Alireza\LaravelFileExplorer\Controllers\DiskController;
use Alireza\LaravelFileExplorer\Controllers\FileExplorerLoaderController;
use Alireza\LaravelFileExplorer\Services\ConfigService;
use Illuminate\Support\Facades\Route;

$configService = resolve(ConfigService::class);

Route::group([
    'middleware' => $configService->getMiddlewares(),
    'prefix'     => $configService->getRoutePrefix()
], function () {
    Route::get('load-file-explorer', [FileExplorerLoaderController::class, 'loadView']);
    Route::get('disks/{disk}', [DiskController::class, 'loadDiskDirs'])->name("lfx.disk");
    Route::get('disks/{diskName}/dirs/{dirName}', [FileExplorerLoaderController::class, 'loadDirItems']);
});
