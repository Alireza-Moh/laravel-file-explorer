<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Requests\CreateDirRequest;
use Alireza\LaravelFileExplorer\Requests\DeleteItemRequest;
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
     * @param Request $request
     * @param DirService $dirService
     * @return JsonResponse directory items.
     */
    public function loadDirItems(string $diskName, string $dirName, Request $request, DirService $dirService): JsonResponse
    {
        if (!$request->has("path")) {
            return response()->json([
                "message" => "Invalid data sent",
                "errors" => [
                    ["path" => "Directory path is missing"]
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $matchedDir = $dirService->findDirectoryByName($diskName, $dirName);
        $selectedDirPath = null;

        if ($matchedDir !== null) {
            $selectedDirPath = $matchedDir['path'];
        }

        $dirPath = urldecode(
            $request->query("path")
        );

        return response()->json([
            "dirName" => $dirName,
            "items" => $dirService->getDirItems($diskName, $dirPath),
            "selectedDirPath" => $selectedDirPath,
        ]);
    }
}
