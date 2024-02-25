<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Requests\CreateDirRequest;
use Alireza\LaravelFileExplorer\Requests\DeleteItemRequest;
use Alireza\LaravelFileExplorer\Requests\PathRequest;
use Alireza\LaravelFileExplorer\Services\DirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

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
