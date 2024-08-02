<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Controllers;

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
            : null;

        $selectedDirPath = $selectedDir
            ? $diskManager->findDirectoryByName($selectedDir->name)->path
            : null;

        $selectedDirItems = $selectedDir
            ? $diskManager->getItemsByDirectoryName($selectedDir->name, $selectedDir->path)
            : [];

        return response()->json([
            'result' => [
                'dirs' => $diskManager->directories,
                'selectedDir' => $selectedDir,
                'selectedDirPath' => $selectedDirPath,
                'selectedDirItems' => $selectedDirItems
            ]
        ]);
    }
}
