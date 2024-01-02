<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Requests\CreateFileRequest;
use Alireza\LaravelFileExplorer\Requests\UploadFilesRequest;
use Alireza\LaravelFileExplorer\Services\FileService;
use Alireza\LaravelFileExplorer\Services\FileSystemService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function renameFile(string $diskName, string $fileName, Request $request, FileService $fileService): JsonResponse
    {
        $validatedData = $request->validate([
            "oldPath" => "required|string",
            "newPath" => "required|string",
        ]);

        $result = $fileService->rename($diskName, $validatedData);

        return response()->json($result);
    }

    public function deleteFile(string $diskName, string $fileName, Request $request, FileService $fileService): JsonResponse
    {
        $validatedData = $request->validate([
            "path" => "required|string",
        ]);

        $result = $fileService->delete($diskName, $validatedData);

        return response()->json($result);
    }

    public function createFile(string $diskName, string $dirName, CreateFileRequest $request, FileSystemService $fileSystemService): JsonResponse
    {
        $validatedData = $request->validated();

        $result = $fileSystemService->create($diskName, $dirName, $validatedData);

        return response()->json($result);
    }

    public function uploadFiles(string $diskName, UploadFilesRequest $request, FileService $fileService): JsonResponse
    {
        $validatedData = $request->validated();

        $result = $fileService->upload($diskName, $validatedData);

        return response()->json($result);
    }

    public function downloadFile(string $diskName, string $dirName, string $fileName, Request $request, FileService $fileService): StreamedResponse|JsonResponse
    {
        $validatedData = $request->validate([
            "path" => "required|string",
            "type" => "required|string"
        ]);

        if($validatedData["type"] === "dir") {
            return $this->getJsonResponse("failed", "Can not download directory", 403);
        }

        try {
            return $fileService->download($diskName, $validatedData);
        } catch (Exception $e) {
            return $this->getJsonResponse("failed", "File not found", 404);
        }
    }

    private function getJsonResponse(string $statusType, string $message, int $httpStatus = 200): JsonResponse
    {
        return response()->json([
            "result" => [
                'status' => $statusType,
                'message' => $message,
            ]
        ], $httpStatus);
    }
}
