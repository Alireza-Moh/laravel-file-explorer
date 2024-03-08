<?php

namespace AlirezaMoh\LaravelFileExplorer\Controllers;

use AlirezaMoh\LaravelFileExplorer\Services\DirService;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

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
            "result" => [
                "dirs" => $dirs,
                "selectedDir" => $selectedDir,
                "selectedDirPath" => $selectedDirPath,
                "selectedDirItems" => $diskItems
            ]
        ]);
    }

    /**
     * Get disk data.
     *
     * @param string $diskName
     * @param array $dirs
     * @param DirService $dirService
     * @return array
     */
    private function getDiskData(string $diskName, array $dirs, DirService $dirService): array
    {
        $diskItems = $dirService->getDiskItems($diskName);
        $selectedDir = "";
        $selectedDirPath = "";

        if (empty($diskItems) && !empty($dirs)) {
            $selectedDir = $dirs[0]["name"];
            $selectedDirPath = $dirs[0]["path"];
            $diskItems = $dirService->getDirItems($diskName, $selectedDir);
        }
        return array($diskItems, $selectedDir, $selectedDirPath);
    }
}
