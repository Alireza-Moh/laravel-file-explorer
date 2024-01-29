<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Services\ConfigRepository;
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
        $defaultDisk = ConfigRepository::getDefaultDiskOnLoading();
        $dirService = new DirService();

        return [
            "disks" => ConfigRepository::getDisks(),
            "dirsForSelectedDisk" => $this->getDirsForSelectedDisk($dirService, $defaultDisk),
            "selectedDisk" => ConfigRepository::getDefaultDiskOnLoading(),
            "selectedDir" => ConfigRepository::getDefaultDirectoryOnLoading(),
            "selectedDirPath" =>  $this->getSelectedDirPath($dirService, $defaultDisk),
            "selectedDirItems" => $dirService->getDirItems($defaultDisk, ConfigRepository::getDefaultDirectoryOnLoading())
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
            "dirs" => $dirService->getDiskDirsForTree($defaultDisk),
            "diskName" => $defaultDisk
        ];
    }

    /**
     * Get the path of the default selected dir
     *
     * @param DirService $dirService
     * @param string $defaultDisk
     * @return string
     */
    private function getSelectedDirPath(DirService $dirService, string $defaultDisk): string
    {
        $dirByLabel = $dirService->findDirectoryByName($defaultDisk, ConfigRepository::getDefaultDirectoryOnLoading());
        $selectedDirPath = "";
        if ($dirByLabel !== null) {
            $selectedDirPath = $dirByLabel['path'];
        }
        return $selectedDirPath;
    }
}
