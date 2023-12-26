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
        $directoryService = new DirService($diskName);
        $dirs = $directoryService->getDiskDirs();

        $selectedDir = "";
        list($dirItems, $selectedDir) = $this->getSelectedDirItems($dirs, $selectedDir, $directoryService);

        return response()->json([
            "dirs" => $dirs,
            "selectedDir" => $selectedDir,
            "selectedDirItems" => $dirItems
        ]);
    }

    /**
     * Get selected directory items.
     *
     * @param array $dirs Array of directories.
     * @param mixed $selectedDir Selected directory name.
     * @param DirService $directoryService Instance of DirService.
     *
     * @return array containing selected directory items and the selected directory name.
     */
    public function getSelectedDirItems(array $dirs, mixed $selectedDir, DirService $directoryService): array
    {
        $dirItems = [];
        if (!empty($dirs)) {
            $selectedDir = $dirs[0]["label"];
            $dirItems = $directoryService->getDirItems($selectedDir);
        } else {
            $dirItems = $directoryService->getDiskFiles();
        }

        return array($dirItems, $selectedDir);
    }
}
