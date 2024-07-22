<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Controllers;

use AlirezaMoh\LaravelFileExplorer\Http\Requests\CreateDirRequest;
use AlirezaMoh\LaravelFileExplorer\Http\Requests\DeleteItemRequest;
use AlirezaMoh\LaravelFileExplorer\Http\Requests\PathRequest;
use AlirezaMoh\LaravelFileExplorer\Services\DirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DirController extends Controller
{
    private DirService $dirService;

    public function __construct(DirService $dirService)
    {
        $this->dirService = $dirService;
    }

    public function deleteDir(string $diskName, DeleteItemRequest $request): JsonResponse
    {
        return $this->dirService->delete($diskName, $request->validated());
    }

    public function createDir(string $diskName, string $dirName, CreateDirRequest $request): JsonResponse
    {
        return $this->dirService->create($diskName, $request->validated());
    }

    public function loadDirItems(string $diskName, string $dirName, PathRequest $pathRequest): JsonResponse
    {
        $matchedDir = $this->dirService->findDirectoryByName($diskName, $dirName);
        $selectedDirPath = null;

        if ($matchedDir !== null) {
            $selectedDirPath = $matchedDir['path'];
        }
        return response()->json([
            'dirName' => $dirName,
            'items' => $this->dirService->getDirItems($diskName, $pathRequest->validated()['path']),
            'selectedDirPath' => $selectedDirPath,
        ]);
    }
}
