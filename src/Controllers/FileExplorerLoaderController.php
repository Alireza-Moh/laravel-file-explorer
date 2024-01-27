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
        return response()->json(
            [
                "result" => [
                    "status" => "success",
                    "data" => $this->getDefaultExplorerDataOnInitialization()
                ]
            ]
        );
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
        $dirService = new DirService();

        return [
            "disks" => $config->getDisks(),
            "dirsForSelectedDisk" => $this->getDirsForSelectedDisk($dirService, $defaultDisk),
            "selectedDisk" => $config->getDefaultDiskOnLoading(),
            "selectedDir" => $config->getDefaultDirectoryOnLoading(),
            "selectedDirPath" =>  $this->getSelectedDirPath($dirService, $defaultDisk, $config),
            "selectedDirItems" => $dirService->getDirItems($defaultDisk, $config->getDefaultDirectoryOnLoading())
        ];
    }

    /**
     *
     * Get the directories of the default selected disk
     * @param DirService $dirService
     * @param string $defaultDisk
     * @return array
     */
    private function getDirsForSelectedDisk(DirService $dirService, string $defaultDisk): array
    {
        return [
            "dirs" => $dirService->getDiskDirs($defaultDisk),
            "diskName" => $defaultDisk
        ];
    }

    /**
     * Get the path of the default selected dir
     *
     * @param DirService $dirService
     * @param string $defaultDisk
     * @param ExplorerConfig $config
     * @return mixed|null
     */
    private function getSelectedDirPath(DirService $dirService, string $defaultDisk, ExplorerConfig $config): string
    {
        $dirByLabel = $dirService->findDirectoryByName($defaultDisk, $config->getDefaultDirectoryOnLoading());
        $selectedDirPath = "";
        if ($dirByLabel !== null) {
            $selectedDirPath = $dirByLabel['path'];
        }
        return $selectedDirPath;
    }
}
