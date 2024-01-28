<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Requests\CreateDirRequest;
use Alireza\LaravelFileExplorer\Requests\DeleteItemRequest;
use Alireza\LaravelFileExplorer\Requests\RenameItemRequest;
use Alireza\LaravelFileExplorer\Services\DirService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DirController extends Controller
{
    /**
     * Rename a directory.
     *
     * @param string $diskName The name of the disk.
     * @param string $dirName the old name of the directory
     * @param RenameItemRequest $request http request
     * @param DirService $dirService
     * @return JsonResponse successful/failed
     */
    public function renameDir(string $diskName, string $dirName, RenameItemRequest $request, DirService $dirService): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $dirService->rename($diskName, $dirName, $validatedData);

        return response()->json($result);
    }

    /**
     * Delete a directory.
     *
     * @param string  $diskName
     * @param DeleteItemRequest $request
     * @param DirService $dirService
     *
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
     * @param string $diskName The name of the disk.
     * @param string $dirName The directory name.
     * @param Request $request
     * @param DirService $dirService
     *
     * @return JsonResponse directory items.
     */
    public function loadDirItems(string $diskName, string $dirName, Request $request, DirService $dirService): JsonResponse
    {
        $validatedData = $request->validate([
            "path" => "required|string",
        ]);

        $dirByLabel = $dirService->findDirectoryByName($diskName, $dirName);
        $selectedDirPath = null;
        if ($dirByLabel !== null) {
            $selectedDirPath = $dirByLabel['path'];
        }

        return response()->json([
            "dirName" => $dirName,
            "items" => $dirService->getDirItems($diskName, $validatedData["path"]),
            "selectedDirPath" => $selectedDirPath,
        ]);
    }
}
