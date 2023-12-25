<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Services\DiskService;
use Alireza\LaravelFileExplorer\Services\ExplorerConfig;
use Alireza\LaravelFileExplorer\Services\DirectoryService;
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
     * Load items from a specified directory.
     *
     * @param string $diskName The name of the disk.
     * @param string $dirName The directory name.
     *
     * @return JsonResponse directory items.
     */
    public function loadDirItems(string $diskName, string $dirName): JsonResponse
    {
        $directoryService = new DirectoryService($diskName);

        return response()->json([
            "dirName" => $dirName,
            "items" => $directoryService->getDirItems($dirName),
        ], 200);
    }

    /**
     * Get default explorer data on initialization.
     *
     * @return array default explorer data.
     */
    public function getDefaultExplorerDataOnInitialization(): array
    {
        $config = new ExplorerConfig();
        $defaultDisk = $config->getDefaultDiskOnLoading();
        $directoryService = new DirectoryService($defaultDisk);

        $dirsForSelectedDisk = [
            "dirs" => $directoryService->getDiskDirs(),
            "diskName" => $defaultDisk
        ];

        return [
            "disks" => $config->getDisks(),
            "dirsForSelectedDisk" => $dirsForSelectedDisk,
            "selectedDisk" => $config->getDefaultDiskOnLoading(),
            "selectedDir" => $config->getDefaultDirectoryOnLoading(),
            "selectedDirItems" => $directoryService->getDirItems($config->getDefaultDirectoryOnLoading()),
        ];
    }
}
