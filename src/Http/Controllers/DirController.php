<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Controllers;

use AlirezaMoh\LaravelFileExplorer\Http\Requests\CreateDirRequest;
use AlirezaMoh\LaravelFileExplorer\Http\Requests\PathRequest;
use AlirezaMoh\LaravelFileExplorer\Services\DirService;
use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\DiskManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DirController extends Controller
{
    private DirService $dirService;

    public function __construct(DirService $dirService)
    {
        $this->dirService = $dirService;
    }

    public function createDir(string $diskName, CreateDirRequest $request): JsonResponse
    {
        return $this->dirService->create($diskName, $request->validated());
    }

    public function loadDirectoryItems(string $diskName, string $dirName, PathRequest $request): JsonResponse
    {
        $diskManager = new DiskManager($diskName);

        return ApiResponse::success(
            '',
            [
                'dirName' => $dirName,
                'items' =>$diskManager->getItemsByParentName($dirName, $request['path']),
                'selectedDirPath' => $request['path']
            ]
        );
    }
}
