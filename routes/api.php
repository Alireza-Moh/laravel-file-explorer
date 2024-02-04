<?php

use Alireza\LaravelFileExplorer\Controllers\DirController;
use Alireza\LaravelFileExplorer\Controllers\DiskController;
use Alireza\LaravelFileExplorer\Controllers\ItemController;
use Alireza\LaravelFileExplorer\Controllers\FileExplorerLoaderController;
use Alireza\LaravelFileExplorer\Services\ConfigRepository;
use Illuminate\Support\Facades\Route;

$configService = resolve(ConfigRepository::class);

Route::group([
    'middleware' => $configService::getMiddlewares(),
    'prefix'     => $configService::getRoutePrefix()
], function () {
    Route::get('load-file-explorer', [FileExplorerLoaderController::class, 'initFileExplorer'])->name("fx.init-file-explorer");
    Route::get('disks/{diskName}', [DiskController::class, 'loadDiskDirs'])->name("fx.disks");

    Route::post('disks/{diskName}/dirs/{dirName}/new-file', [ItemController::class, 'createFile'])->name("fx.file-create");
    Route::post('disks/{diskName}/dirs/{dirName}/new-dir', [DirController::class, 'createDir'])->name("fx.dir-create");
    Route::post('disks/{diskName}/files/upload', [ItemController::class, 'uploadFiles'])->name("fx.file-upload");
    Route::post('disks/{diskName}/files/download', [ItemController::class, 'downloadFile'])->name("fx.file-download");
    Route::post('disks/{diskName}/dirs/{dirName}', [DirController::class, 'loadDirItems'])->name("fx.load-dir-items");

    Route::put('disks/{diskName}/files/{dirName}', [ItemController::class, 'renameFile'])->name("fx.file-rename");
    Route::put('disks/{diskName}/dirs/{dirName}', [DirController::class, 'renameDir'])->name("fx.dir-rename");

    Route::delete('disks/{diskName}/files/delete', [ItemController::class, 'deleteFile'])->name("fx.file-delete");
    Route::delete('disks/{diskName}/dirs/delete', [DirController::class, 'deleteDir'])->name("fx.dir-delete");
});
