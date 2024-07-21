<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Controllers;

use AlirezaMoh\LaravelFileExplorer\Http\Requests\CreateFileRequest;
use AlirezaMoh\LaravelFileExplorer\Http\Requests\DeleteItemRequest;
use AlirezaMoh\LaravelFileExplorer\Http\Requests\DownloadFileRequest;
use AlirezaMoh\LaravelFileExplorer\Http\Requests\PathRequest;
use AlirezaMoh\LaravelFileExplorer\Http\Requests\RenameItemRequest;
use AlirezaMoh\LaravelFileExplorer\Http\Requests\UpdateItemContentRequest;
use AlirezaMoh\LaravelFileExplorer\Http\Requests\UploadItemsRequest;
use AlirezaMoh\LaravelFileExplorer\Services\ItemService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemController extends Controller
{
    private ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * Rename a file.
     *
     * @param string $diskName
     * @param RenameItemRequest $request
     * @return JsonResponse
     */
    public function renameItem(string $diskName, RenameItemRequest $request): JsonResponse
    {
        $result = $this->itemService->rename($diskName, $request->validated());
        return response()->json($result);
    }

    /**
     * Delete a file.
     *
     * @param string $diskName
     * @param DeleteItemRequest $request
     * @return JsonResponse
     */
    public function deleteItems(string $diskName, DeleteItemRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $this->itemService->delete($diskName, $validatedData);

        return response()->json($result);
    }

    /**
     * Create a file.
     *
     * @param string $diskName
     * @param string $dirName
     * @param CreateFileRequest $request
     * @return JsonResponse
     */
    public function createFile(string $diskName, string $dirName, CreateFileRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $this->itemService->create($diskName, $validatedData);

        return response()->json($result);
    }

    /**
     * Upload files.
     *
     * @param string $diskName
     * @param UploadItemsRequest $request
     * @return JsonResponse
     */
    public function uploadItems(string $diskName, UploadItemsRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $result = $this->itemService->upload($diskName, $validatedData);

        return response()->json($result);
    }

    /**
     * Download item/items.
     *
     * @param string $diskName
     * @param DownloadFileRequest $request
     * @return BinaryFileResponse|JsonResponse|StreamedResponse
     * @throws Exception
     */
    public function downloadItems(string $diskName, DownloadFileRequest $request): BinaryFileResponse|JsonResponse|StreamedResponse
    {
        return $this->itemService->download($diskName, $request->validated());
    }

    /**
     * Get File content for editor
     *
     * @param string $diskName
     * @param string $itemName
     * @param PathRequest $pathRequest
     * @return JsonResponse
     */
    public function getContent(string $diskName, string $itemName, PathRequest $pathRequest): JsonResponse
    {
        return response()->json([
            "result" => [
                "content" => $this->itemService->getItemContent($diskName, $pathRequest->validated()),
            ]
        ]);
    }

    public function updateContent(string $diskName, string $itemName, UpdateItemContentRequest $updateItemContentRequest): JsonResponse
    {
        $result = $this->itemService->updateItemContent($diskName, $updateItemContentRequest->validated());
        return response()->json($result);
    }
}
