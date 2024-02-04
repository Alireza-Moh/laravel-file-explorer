<?php

namespace Alireza\LaravelFileExplorer\Controllers;

use Alireza\LaravelFileExplorer\Requests\CreateFileRequest;
use Alireza\LaravelFileExplorer\Requests\DeleteItemRequest;
use Alireza\LaravelFileExplorer\Requests\DownloadFileRequest;
use Alireza\LaravelFileExplorer\Requests\RenameItemRequest;
use Alireza\LaravelFileExplorer\Requests\UploadItemsRequest;
use Alireza\LaravelFileExplorer\Services\ItemService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemController extends Controller
{
    /**
     * Rename a file.
     *
     * @param string $diskName
     * @param string $fileName
     * @param RenameItemRequest $request
     * @param ItemService $fileService
     * @return JsonResponse
     */
    public function renameFile(string $diskName, string $fileName, RenameItemRequest $request, ItemService $fileService): JsonResponse
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
     * @param ItemService $fileService
     * @return JsonResponse
     */
    public function deleteFile(string $diskName, DeleteItemRequest $request, ItemService $fileService): JsonResponse
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
     * @param ItemService $fileService
     * @return JsonResponse
     */
    public function createFile(string $diskName, string $dirName, CreateFileRequest $request, ItemService $fileService): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $fileService->create($diskName, $validatedData);

        return response()->json($result);
    }

    /**
     * Upload files.
     *
     * @param string $diskName
     * @param UploadItemsRequest $request
     * @param ItemService $fileService
     * @return JsonResponse
     */
    public function uploadFiles(string $diskName, UploadItemsRequest $request, ItemService $fileService): JsonResponse
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
     * @param ItemService $fileService
     * @return BinaryFileResponse|JsonResponse|StreamedResponse
     */
    public function downloadFile(string $diskName, DownloadFileRequest $request, ItemService $fileService): BinaryFileResponse|JsonResponse|StreamedResponse
    {
        $validatedData = $request->validated();

        if (count($validatedData["items"]) === 1) {
            return $this->downloadSingleItem($fileService, $diskName, $validatedData);
        }

        return $fileService->downloadAsZip($diskName, $validatedData);
    }

    /**
     * Download a single file.
     *
     * @param ItemService $fileService
     * @param string $diskName
     * @param array $validatedData
     * @return JsonResponse|StreamedResponse
     */
    public function downloadSingleItem(ItemService $fileService, string $diskName, array $validatedData): JsonResponse|StreamedResponse
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
