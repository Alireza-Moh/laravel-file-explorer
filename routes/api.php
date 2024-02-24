<?php

use Alireza\LaravelFileExplorer\Controllers\DirController;
use Alireza\LaravelFileExplorer\Controllers\DiskController;
use Alireza\LaravelFileExplorer\Controllers\ItemController;
use Alireza\LaravelFileExplorer\Controllers\ExplorerInitDataController;
use Alireza\LaravelFileExplorer\Services\ConfigRepository;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ConfigRepository::getMiddlewares(),
    'prefix' => ConfigRepository::getRoutePrefix()
], function () {

    Route::get('init-explorer', [ExplorerInitDataController::class, 'initExplorer'])->name("fx.init-explorer");

    Route::middleware("validate.disk")->group(function () {
        Route::get('disks/{diskName}', [DiskController::class, 'loadDiskDirs'])->name("fx.disks");
        Route::get('disks/{diskName}/items/{itemName}', [ItemController::class, 'getContent'])->name("fx.get-item-content");
        Route::get('disks/{diskName}/dirs/{dirName}', [DirController::class, 'loadDirItems'])->name("fx.load-dir-items");

        Route::post('disks/{diskName}/dirs/{dirName}/new-file', [ItemController::class, 'createFile'])->name("fx.file-create");
        Route::post('disks/{diskName}/dirs/{dirName}/new-dir', [DirController::class, 'createDir'])->name("fx.dir-create");
        Route::post('disks/{diskName}/items/upload', [ItemController::class, 'uploadItems'])->name("fx.items-upload");
        Route::post('disks/{diskName}/items/download', [ItemController::class, 'downloadItems'])->name("fx.items-download");
        Route::post('disks/{diskName}/dirs/{dirName}', [ItemController::class, 'renameItem'])->name("fx.item-rename");
        Route::post('disks/{diskName}/items/{itemName}', [ItemController::class, 'updateContent'])->name("fx.update-item-content");

        Route::delete('disks/{diskName}/items/delete', [ItemController::class, 'deleteItems'])->name("fx.items-delete");
        Route::delete('disks/{diskName}/dirs/delete', [DirController::class, 'deleteDir'])->name("fx.dir-delete");
    });

});
