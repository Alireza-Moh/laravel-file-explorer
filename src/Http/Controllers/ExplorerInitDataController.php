<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Controllers;

use AlirezaMoh\LaravelFileExplorer\Services\DirService;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ExplorerInitDataController extends Controller
{

    private DirService $dirService;

    public function __construct(DirService $dirService)
    {
        $this->dirService = $dirService;
    }

    public function initExplorer(): JsonResponse
    {
        return response()->json(
            [
                'result' => [
                    'status' => 'success',
                    'data' => $this->getDefaultExplorerDataOnInitialization()
                ]
            ]
        );
    }

    private function getDefaultExplorerDataOnInitialization(): array
    {
        $defaultDisk = ConfigRepository::getDefaultDiskOnLoading();
        $dirService = new DirService();
        $defaultDir = ConfigRepository::getDefaultDirectoryOnLoading();

        return [
            'disks' => ConfigRepository::getDisks(),
            'dirsForSelectedDisk' => $this->getDirsForSelectedDisk($defaultDisk),
            'selectedDisk' => ConfigRepository::getDefaultDiskOnLoading(),
            'selectedDir' => ConfigRepository::getDefaultDirectoryOnLoading(),
            'selectedDirPath' =>  $this->getSelectedDirPath($defaultDisk),
            'selectedDirItems' => $defaultDir ? $dirService->getDirItems($defaultDisk, $defaultDir) : []
        ];
    }

    private function getDirsForSelectedDisk(string $defaultDisk): array
    {
        return [
            'dirs' => $this->dirService->getDiskDirsForTree($defaultDisk),
            'diskName' => $defaultDisk
        ];
    }

    private function getSelectedDirPath(string $defaultDisk): string
    {
        $defaultDir = ConfigRepository::getDefaultDirectoryOnLoading();
        if (is_null($defaultDir)) {
            return '';
        }
        $dirByLabel = $this->dirService->findDirectoryByName($defaultDisk, $defaultDir);
        $selectedDirPath = '';
        if ($dirByLabel !== null) {
            $selectedDirPath = $dirByLabel['path'];
        }
        return $selectedDirPath;
    }
}
