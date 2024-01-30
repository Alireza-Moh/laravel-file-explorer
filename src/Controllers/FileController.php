<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Requests\CreateFileRequest;
use Alireza\LaravelFileExplorer\Requests\DeleteItemRequest;
use Alireza\LaravelFileExplorer\Requests\DownloadFileRequest;
use Alireza\LaravelFileExplorer\Requests\RenameItemRequest;
use Alireza\LaravelFileExplorer\Requests\UploadFilesRequest;
use Alireza\LaravelFileExplorer\Services\FileService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Rename a file.
     *
     * @param string $diskName
     * @param string $fileName
     * @param RenameItemRequest $request
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function renameFile(string $diskName, string $fileName, RenameItemRequest $request, FileService $fileService): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $fileService->rename($diskName, $fileName, $validatedData);

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
     * @param FileService $fileService
     * @return JsonResponse
     */
    public function createFile(string $diskName, string $dirName, CreateFileRequest $request, FileService $fileService): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $fileService->create($diskName, $validatedData);

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
     *
     * @return BinaryFileResponse|JsonResponse|StreamedResponse
     */
    public function downloadFile(string $diskName, DownloadFileRequest $request, FileService $fileService): BinaryFileResponse|JsonResponse|StreamedResponse
    {
        $validatedData = $request->validated();

        if (count($validatedData["files"]) === 1) {
            return $this->downloadSingleItem($fileService, $diskName, $validatedData);
        }

        return $fileService->downloadAsZip($diskName, $validatedData);
    }

    /**
     * @param FileService $fileService
     * @param string $diskName
     * @param mixed $validatedData
     * @return JsonResponse|StreamedResponse
     */
    public function downloadSingleItem(FileService $fileService, string $diskName, mixed $validatedData): JsonResponse|StreamedResponse
    {
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
}
