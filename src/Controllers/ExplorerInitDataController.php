<?php

namespace AlirezaMoh\LaravelFileExplorer\Controllers;

use AlirezaMoh\LaravelFileExplorer\Services\ConfigRepository;
use AlirezaMoh\LaravelFileExplorer\Services\DirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ExplorerInitDataController extends Controller
{
    /**
     * Initialize the file explorer
     *
     * @return JsonResponse
     */
    public function initExplorer(): JsonResponse
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
     * @return array
     */
    private function getDefaultExplorerDataOnInitialization(): array
    {
        $defaultDisk = ConfigRepository::getDefaultDiskOnLoading();
        $dirService = new DirService();
        $defaultDir = ConfigRepository::getDefaultDirectoryOnLoading();

        return [
            "disks" => ConfigRepository::getDisks(),
            "dirsForSelectedDisk" => $this->getDirsForSelectedDisk($dirService, $defaultDisk),
            "selectedDisk" => ConfigRepository::getDefaultDiskOnLoading(),
            "selectedDir" => ConfigRepository::getDefaultDirectoryOnLoading(),
            "selectedDirPath" =>  $this->getSelectedDirPath($dirService, $defaultDisk),
            "selectedDirItems" => $defaultDir ? $dirService->getDirItems($defaultDisk, $defaultDir) : []
        ];
    }

    /**
     * Get the directories of the default selected disk
     *
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
        $defaultDir = ConfigRepository::getDefaultDirectoryOnLoading();
        if (is_null($defaultDir)) {
            return "";
        }
        $dirByLabel = $dirService->findDirectoryByName($defaultDisk, $defaultDir);
        $selectedDirPath = "";
        if ($dirByLabel !== null) {
            $selectedDirPath = $dirByLabel['path'];
        }
        return $selectedDirPath;
    }
}
