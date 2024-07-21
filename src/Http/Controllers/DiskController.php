<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Controllers;

use AlirezaMoh\LaravelFileExplorer\Services\DirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DiskController extends Controller
{
    private DirService $dirService;

    public function __construct(DirService $dirService)
    {
        $this->dirService = $dirService;
    }

    /**
     * Load directories for a specified disk.
     *
     * @param string $diskName
     * @return JsonResponse
     */
    public function loadDiskDirs(string $diskName): JsonResponse
    {
        $dirs = $this->dirService->getDiskDirsForTree($diskName);

        list($diskItems, $selectedDir, $selectedDirPath) = $this->getDiskData($diskName, $dirs);

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
     * @return array
     */
    private function getDiskData(string $diskName, array $dirs): array
    {
        $diskItems = $this->dirService->getDiskItems($diskName);
        $selectedDir = "";
        $selectedDirPath = "";

        if (empty($diskItems) && !empty($dirs)) {
            $selectedDir = $dirs[0]["name"];
            $selectedDirPath = $dirs[0]["path"];
            $diskItems = $this->dirService->getDirItems($diskName, $selectedDir);
        }
        return array($diskItems, $selectedDir, $selectedDirPath);
    }
}
