<?php

namespace AlirezaMoh\LaravelFileExplorer\Controllers;

use AlirezaMoh\LaravelFileExplorer\Requests\CreateDirRequest;
use AlirezaMoh\LaravelFileExplorer\Requests\DeleteItemRequest;
use AlirezaMoh\LaravelFileExplorer\Requests\PathRequest;
use AlirezaMoh\LaravelFileExplorer\Services\DirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class DirController extends Controller
{
    /**
     * Delete a directory.
     *
     * @param string  $diskName
     * @param DeleteItemRequest $request
     * @param DirService $dirService
     * @return JsonResponse
     */
    public function deleteDir(string $diskName, DeleteItemRequest $request, DirService $dirService): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $dirService->delete($diskName, $validatedData);

        return response()->json($result);
    }

    /**
     * Create a directory.
     *
     * @param string $diskName
     * @param string $dirName
     * @param CreateDirRequest $request
     * @param DirService $dirService
     * @return JsonResponse
     */
    public function createDir(string $diskName, string $dirName, CreateDirRequest $request, DirService $dirService): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $dirService->create($diskName, $validatedData);

        return response()->json($result);
    }

    /**
     * Load items from a specified directory.
     *
     * @param string $diskName
     * @param string $dirName
     * @param PathRequest $pathRequest
     * @param DirService $dirService
     * @return JsonResponse
     */
    public function loadDirItems(string $diskName, string $dirName, PathRequest $pathRequest, DirService $dirService): JsonResponse
    {
        $matchedDir = $dirService->findDirectoryByName($diskName, $dirName);
        $selectedDirPath = null;

        if ($matchedDir !== null) {
            $selectedDirPath = $matchedDir['path'];
        }
        return response()->json([
            "dirName" => $dirName,
            "items" => $dirService->getDirItems($diskName, $pathRequest->validated()["path"]),
            "selectedDirPath" => $selectedDirPath,
        ]);
    }
}
