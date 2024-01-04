<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Requests\CreateDirRequest;
use Alireza\LaravelFileExplorer\Requests\DeleteItemRequest;
use Alireza\LaravelFileExplorer\Requests\RenameItemRequest;
use Alireza\LaravelFileExplorer\Services\DirService;
use Alireza\LaravelFileExplorer\Services\FileSystemService;
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
     * @param FileSystemService $fileSystemService
     *
     * @return JsonResponse successful/failed
     */
    public function renameDir(string $diskName, string $dirName, RenameItemRequest $request, FileSystemService $fileSystemService): JsonResponse
    {
        $validatedData = $request->validated();

        $result = $fileSystemService->renameDir($diskName, $dirName, $validatedData);

        return response()->json($result);
    }

    /**
     * Delete a directory.
     *
     * @param string  $diskName
     * @param DeleteItemRequest $request
     *
     * @return JsonResponse
     */
    public function deleteDir(string $diskName, DeleteItemRequest $request): JsonResponse
    {
        $dirService = new DirService($diskName);
        $validatedData = $request->validated();

        $result = $dirService->delete($diskName, $validatedData);
        return response()->json(["result" => $result]);
    }

    /**
     * Create a directory.
     *
     * @param string $diskName
     * @param string $dirName
     * @param CreateDirRequest $request
     * @param FileSystemService $fileSystemService
     * @return JsonResponse
     */
    public function createDir(string $diskName, string $dirName, CreateDirRequest $request, FileSystemService $fileSystemService): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $fileSystemService->create($diskName, $dirName, $validatedData);

        return response()->json($result);
    }

    /**
     * Load items from a specified directory.
     *
     * @param string $diskName The name of the disk.
     * @param string $dirName The directory name.
     *
     * @return JsonResponse directory items.
     */
    public function loadDirItems(string $diskName, string $dirName, Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            "path" => "required|string",
        ]);
        $dirService = new DirService($diskName);

        $dirByLabel = $dirService->findDirectoryByLabel($dirName);
        $selectedDirPath = null;
        if ($dirByLabel !== null) {
            $selectedDirPath = $dirByLabel['path'];
        }

        return response()->json([
            "dirName" => $dirName,
            "items" => $dirService->getDirItems($validatedData["path"]),
            "selectedDirPath" => $selectedDirPath,
        ]);
    }
}
