<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Controllers;

use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\DiskManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DiskController extends Controller
{
    public function loadDiskDirs(string $diskName): JsonResponse
    {
        $diskManager = new DiskManager($diskName);

        $selectedDir = $diskManager->directories->isNotEmpty()
            ? $diskManager->directories->first()
            : "";


        $selectedDirPath = $selectedDir
            ? $diskManager->findDirectoryByName($selectedDir->name)->path
            : "";

        $selectedDirItems = $diskManager->diskFiles->isNotEmpty()
            ? $diskManager->diskFiles
            : ($selectedDir
                ? $diskManager->getItemsByParentName($selectedDir->name, $selectedDir->path)
                : []
            );

        return ApiResponse::success(
            "",
            [
                'dirs' => $diskManager->directories,
                'selectedDir' => $selectedDir,
                'selectedDirPath' => $selectedDirPath,
                'selectedDirItems' => $selectedDirItems
            ]
        );
    }
}
