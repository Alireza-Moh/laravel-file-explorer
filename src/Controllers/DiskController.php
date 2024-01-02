<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Services\DirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DiskController extends Controller
{
    /**
     * Load directories for a specified disk.
     *
     * @param string $diskName The name of the disk.
     *
     * @return JsonResponse The directories of the disk
     */
    public function loadDiskDirs(string $diskName): JsonResponse
    {
        $dirService = new DirService($diskName);
        $dirs = $dirService->getDiskDirs();

        $selectedDir = "";
        list($dirItems, $selectedDir, $selectedDirPath) = $this->getSelectedDirItems($dirs, $selectedDir, $dirService);

        return response()->json([
            "dirs" => $dirs,
            "selectedDir" => $selectedDir,
            "selectedDirPath" => $selectedDirPath,
            "selectedDirItems" => $dirItems
        ]);
    }

    /**
     * Get selected directory items.
     *
     * @param array $dirs Array of directories.
     * @param mixed $selectedDir Selected directory name.
     * @param DirService $dirService
     *
     * @return array containing selected directory items and the selected directory name.
     */
    public function getSelectedDirItems(array $dirs, mixed $selectedDir, DirService $dirService): array
    {
        $dirItems = [];
        if (!empty($dirs)) {
            $selectedDir = $dirs[0]["label"];
            $selectedDirPath = $dirs[0]["path"];
            $dirItems = $dirService->getDirItems($selectedDir);
        } else {
            $dirItems = $dirService->getDiskFiles($selectedDir);
            $selectedDirPath = $selectedDir;
        }

        return array($dirItems, $selectedDir, $selectedDirPath);
    }
}
