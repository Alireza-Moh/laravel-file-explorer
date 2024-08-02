<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Controllers;

use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use AlirezaMoh\LaravelFileExplorer\Supports\DiskManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ExplorerInitDataController extends Controller
{
    private DiskManager $diskManager;

    public function __construct()
    {
        $this->diskManager = new DiskManager(ConfigRepository::getDefaultDiskOnLoading());
    }

    public function initExplorer(): JsonResponse
    {
        return ApiResponse::success('', $this->getDefaultExplorerDataOnInitialization());
    }

    private function getDefaultExplorerDataOnInitialization(): array
    {
        $defaultDir = ConfigRepository::getDefaultDirectoryOnLoading();

        $selectedDirPath = $defaultDir
            ? $this->diskManager->findDirectoryByName($defaultDir)?->path
            : '';

        $selectedDirItems = $defaultDir
            ? $this->diskManager->getItemsByDirectoryName($defaultDir, $defaultDir)
            : [];

        return [
            'disks' => ConfigRepository::getDisks(),
            'dirsForSelectedDisk' => $this->diskManager->directories,
            'selectedDisk' => ConfigRepository::getDefaultDiskOnLoading(),
            'selectedDir' => ConfigRepository::getDefaultDirectoryOnLoading(),
            'selectedDirPath' => $selectedDirPath,
            'selectedDirItems' => $selectedDirItems
        ];
    }
}
