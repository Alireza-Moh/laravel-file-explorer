<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Requests\CreateFileRequest;
use Alireza\LaravelFileExplorer\Requests\DeleteItemRequest;
use Alireza\LaravelFileExplorer\Requests\DownloadFileRequest;
use Alireza\LaravelFileExplorer\Requests\UploadFilesRequest;
use Alireza\LaravelFileExplorer\Services\FileService;
use Alireza\LaravelFileExplorer\Services\FileSystemService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class FileController extends Controller
{
    /**
     * Rename a file.
     *
     * @param string $diskName
     * @param string $fileName
     * @param Request $request
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function renameFile(string $diskName, string $fileName, Request $request, FileService $fileService): JsonResponse
    {
        $validatedData = $request->validate([
            "oldPath" => "required|string",
            "newPath" => "required|string",
        ]);

        $result = $fileService->rename($diskName, $validatedData);

        return response()->json($result);
    }

    /**
     * Delete a file.
     *
     * @param string $diskName
     * @param DeleteItemRequest $request
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function deleteFile(string $diskName, DeleteItemRequest $request, FileService $fileService): JsonResponse
    {
        $validatedData = $request->validated();

        $result = $fileService->delete($diskName, $validatedData);

        return response()->json($result);
    }

    /**
     * Create a file.
     *
     * @param string $diskName
     * @param string $dirName
     * @param CreateFileRequest $request
     * @param FileSystemService $fileSystemService
     * @return JsonResponse
     */
    public function createFile(string $diskName, string $dirName, CreateFileRequest $request, FileSystemService $fileSystemService): JsonResponse
    {
        $validatedData = $request->validated();

        $result = $fileSystemService->create($diskName, $dirName, $validatedData);

        return response()->json($result);
    }

    /**
     * Upload files.
     *
     * @param string $diskName
     * @param UploadFilesRequest $request
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function uploadFiles(string $diskName, UploadFilesRequest $request, FileService $fileService): JsonResponse
    {
        $validatedData = $request->validated();

        $result = $fileService->upload($diskName, $validatedData);

        return response()->json($result);
    }

    /**
     * Download a file.
     *
     * @param string $diskName
     * @param DownloadFileRequest $request
     * @param FileService $fileService
     * @return BinaryFileResponse|JsonResponse|StreamedResponse
     */
    public function downloadFile(string $diskName, DownloadFileRequest $request, FileService $fileService): BinaryFileResponse|JsonResponse|StreamedResponse
    {
        $validatedData = $request->validated();

        if (count($validatedData["files"]) === 1) {
            try {
                return $fileService->download($diskName, $validatedData);
            } catch (Exception $e) {
                return response()->json([
                    "result" => [
                        "status" => "failed",
                        "message" => "Failed to download files"
                    ]
                ], 404);
            }
        }

        return $fileService->downloadAsZip($diskName, $validatedData);
    }
}
