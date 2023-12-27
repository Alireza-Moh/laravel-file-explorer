<?php

use Alireza\LaravelFileExplorer\Controllers\DirController;
use Alireza\LaravelFileExplorer\Controllers\DiskController;
use Alireza\LaravelFileExplorer\Controllers\FileController;
use Alireza\LaravelFileExplorer\Controllers\FileExplorerLoaderController;
use Alireza\LaravelFileExplorer\Services\ExplorerConfig;
use Illuminate\Support\Facades\Route;

$configService = resolve(ExplorerConfig::class);

Route::group([
    'middleware' => $configService->getMiddlewares(),
    'prefix'     => $configService->getRoutePrefix()
], function () {
    Route::get('load-file-explorer', [FileExplorerLoaderController::class, 'initFileExplorer']);
    Route::get('disks/{diskName}', [DiskController::class, 'loadDiskDirs']);
    Route::get('disks/{diskName}/dirs/{dirName}', [FileExplorerLoaderController::class, 'loadDirItems']);

    Route::post('disks/{diskName}/dirs/{dirName}/new-file', [FileController::class, 'createFile']);
    Route::post('disks/{diskName}/dirs/{dirName}/new-dir', [DirController::class, 'createDir']);

    Route::put('disks/{diskName}/dirs/{dirName}', [DirController::class, 'renameDir']);
    Route::put('disks/{diskName}/files/{dirName}', [FileController::class, 'renameFile']);

    Route::delete('disks/{diskName}/dirs/{dirName}', [DirController::class, 'deleteDir']);
    Route::delete('disks/{diskName}/files/{fileName}', [FileController::class, 'deleteFile']);
});
