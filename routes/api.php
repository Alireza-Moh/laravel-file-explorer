<?php

use AlirezaMoh\LaravelFileExplorer\Http\Controllers\CsrfCookieController;
use AlirezaMoh\LaravelFileExplorer\Http\Controllers\DirController;
use AlirezaMoh\LaravelFileExplorer\Http\Controllers\DiskController;
use AlirezaMoh\LaravelFileExplorer\Http\Controllers\ExplorerInitDataController;
use AlirezaMoh\LaravelFileExplorer\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('init-explorer', [ExplorerInitDataController::class, 'initExplorer'])->name("fx.init-explorer");
Route::get('csrf', [CsrfCookieController::class, 'getCsrf'])->name("fx.csrf");

Route::middleware("validateDisk")->group(function () {
    Route::get('disks/{diskName}', [DiskController::class, 'loadDiskDirs'])
        ->name("fx.disks");

    Route::get('disks/{diskName}/content/items/{itemName}', [ItemController::class, 'getContent'])
        ->name("fx.get-item-content")
        ->middleware('checkPermission:read');

    Route::get('disks/{diskName}/dirs/{dirName}', [DirController::class, 'loadDirectoryItems'])
        ->name("fx.load-dir-items");

    Route::post('disks/{diskName}/dirs/{dirName}/new-file', [ItemController::class, 'createFile'])
        ->name("fx.file-create")
        ->middleware('checkPermission:create');

    Route::post('disks/{diskName}/dirs/{dirName}/new-dir', [DirController::class, 'createDir'])
        ->name("fx.dir-create")
        ->middleware('checkPermission:create');

    Route::post('disks/{diskName}/items/upload', [ItemController::class, 'uploadItems'])
        ->name("fx.items-upload")
        ->middleware('checkPermission:upload');

    Route::post('disks/{diskName}/items/download', [ItemController::class, 'downloadItems'])
        ->name("fx.items-download")
        ->middleware('checkPermission:download');

    Route::post('disks/{diskName}/items/rename', [ItemController::class, 'renameItem'])
        ->name("fx.item-rename")
        ->middleware('checkPermission:update');

    Route::post('disks/{diskName}/items/{itemName}', [ItemController::class, 'updateContent'])
        ->name("fx.update-item-content")
        ->middleware('checkPermission:write');

    Route::post('disks/{diskName}/all/items/delete', [ItemController::class, 'deleteItems'])
        ->name("fx.items-delete")
        ->middleware('checkPermission:delete');
});
