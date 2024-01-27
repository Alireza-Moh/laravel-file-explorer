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
     * @param string $diskName
     * @param DirService $dirService
     * @return JsonResponse
     */
    public function loadDiskDirs(string $diskName, DirService $dirService): JsonResponse
    {
        $dirs = $dirService->getDiskDirs($diskName);

        list($dirItems, $selectedDir, $selectedDirPath) = $this->getSelectedDirItems($diskName, $dirs, $dirService);

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
     * @param string $diskName
     * @param array $dirs
     * @param DirService $dirService
     *
     * @return array containing
     */
    private function getSelectedDirItems(string $diskName, array $dirs, DirService $dirService): array
    {
        $selectedDir = "";
        if (!empty($dirs)) {
            $selectedDir = $dirs[0]["name"];
            $selectedDirPath = $dirs[0]["path"];
            $dirItems = $dirService->getDirItems($diskName, $selectedDir);
        } else {
            $dirItems = $dirService->getDiskFiles($diskName, $selectedDir);
            $selectedDirPath = $selectedDir;
        }

        return array($dirItems, $selectedDir, $selectedDirPath);
    }
}
