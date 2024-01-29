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
        $dirs = $dirService->getDiskDirsForTree($diskName);

        list($diskItems, $selectedDir, $selectedDirPath) = $this->getDiskData($diskName, $dirs, $dirService);

        return response()->json([
            "dirs" => $dirs,
            "selectedDir" => $selectedDir,
            "selectedDirPath" => $selectedDirPath,
            "selectedDirItems" => $diskItems
        ]);
    }

    /**
     * Get disk data.
     *
     * @param string $diskName
     * @param array $dirs
     * @param DirService $dirService
     *
     * @return array containing
     */
    private function getDiskData(string $diskName, array $dirs, DirService $dirService): array
    {
        $diskItems = $dirService->getDiskItems($diskName);
        $selectedDir = "";
        $selectedDirPath = "";
        if (empty($diskItems)) {
            if (!empty($dirs)) {
                $selectedDir = $dirs[0]["name"];
                $selectedDirPath = $dirs[0]["path"];
                $diskItems = $dirService->getDirItems($diskName, $selectedDir);
            }
        }
        return array($diskItems, $selectedDir, $selectedDirPath);
    }
}
