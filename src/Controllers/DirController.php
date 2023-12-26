<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Services\FileSystemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DirController extends Controller
{
    /**
     * Rename a directory.
     *
     * @param string            $diskName           The name of the disk.
     * @param Request           $request
     * @param FileSystemService $fileSystemService
     *
     * @return JsonResponse successful/failed
     */
    public function renameDir(string $diskName, Request $request, FileSystemService $fileSystemService): JsonResponse
    {
        $validatedData = $request->validate([
            "oldName" => "required|string",
            "oldPath" => "required|string",
            "newPath" => "required|string",
        ]);

        $result = $fileSystemService->renameDir($diskName, $validatedData["oldName"], $validatedData["oldPath"], $validatedData["newPath"]);

        return response()->json($result);
    }

    /**
     * Delete a directory.
     *
     * @param string            $diskName           The name of the disk.
     * @param string            $dirName            The directory name to delete.
     * @param Request           $request
     * @param FileSystemService $fileSystemService
     *
     * @return JsonResponse successful/failed
     */
    public function deleteDir(string $diskName, string $dirName, Request $request, FileSystemService $fileSystemService): JsonResponse
    {
        $validatedData = $request->validate([
            "path" => "required|string",
        ]);

        $result = $fileSystemService->deleteDir($diskName, $dirName, $validatedData["path"]);
        return response()->json($result);
    }
}
