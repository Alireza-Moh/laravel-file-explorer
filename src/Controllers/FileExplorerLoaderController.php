<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Services\ExplorerConfig;
use Alireza\LaravelFileExplorer\Services\DirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class FileExplorerLoaderController extends Controller
{
    /**
     * Initialize the file explorer
     *
     * @return JsonResponse initialization data.
     */
    public function initFileExplorer(): JsonResponse
    {
        return response()->json([
            "status" => 200,
            "data" => $this->getDefaultExplorerDataOnInitialization()
        ]);
    }

    /**
     * Get default explorer data on initialization.
     *
     * @return array default explorer data.
     */
    private function getDefaultExplorerDataOnInitialization(): array
    {
        $config = new ExplorerConfig();
        $defaultDisk = $config->getDefaultDiskOnLoading();
        $dirService = new DirService($defaultDisk);

        $dirsForSelectedDisk = [
            "dirs" => $dirService->getDiskDirs(),
            "diskName" => $defaultDisk
        ];

        $dirByLabel = $dirService->findDirectoryByLabel($config->getDefaultDirectoryOnLoading());
        $selectedDirPath = null;
        if ($dirByLabel !== null) {
            $selectedDirPath = $dirByLabel['path'];
        }

        return [
            "disks" => $config->getDisks(),
            "dirsForSelectedDisk" => $dirsForSelectedDisk,
            "selectedDisk" => $config->getDefaultDiskOnLoading(),
            "selectedDir" => $config->getDefaultDirectoryOnLoading(),
            "selectedDirPath" => $selectedDirPath,
            "selectedDirItems" => $dirService->getDirItems($config->getDefaultDirectoryOnLoading())
        ];
    }
}
