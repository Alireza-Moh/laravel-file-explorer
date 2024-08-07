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
use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemController extends Controller
{
    private ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function renameItem(string $diskName, RenameItemRequest $request): JsonResponse
    {
        return $this->itemService->rename($diskName, $request->validated());
    }

    public function deleteItems(string $diskName, DeleteItemRequest $request): JsonResponse
    {
        return $this->itemService->delete($diskName, $request->validated());
    }

    public function createFile(string $diskName, CreateFileRequest $request): JsonResponse
    {
        return $this->itemService->create($diskName, $request->validated());
    }

    public function uploadItems(string $diskName, UploadItemsRequest $request): JsonResponse
    {
        return $this->itemService->upload($diskName, $request->validated());
    }

    public function downloadItems(string $diskName, DownloadFileRequest $request): BinaryFileResponse|JsonResponse|StreamedResponse
    {
        return $this->itemService->download($diskName, $request->validated());
    }

    public function getContent(string $diskName, PathRequest $request): JsonResponse
    {
        $data = [
            'content' => $this->itemService->getItemContent($diskName, $request->validated()),
            'readOnly' => false
        ];

        if (ConfigRepository::isACLEnabled()) {
            $data['readOnly'] = ! $request->user()->hasPermission('write');
        }

        return ApiResponse::success(
            '',
            $data
        );
    }

    public function updateContent(string $diskName, UpdateItemContentRequest $request): JsonResponse
    {
        return $this->itemService->updateItemContent(
            $diskName,
            [
                'path' => $request->validated()['path'],
                'item' => $request->file('item')->getContent(),
            ]
        );
    }

    public function getUrl(string $diskName, PathRequest $request)
    {
        return ApiResponse::success(
            '',
            [
                'url' => Storage::disk($diskName)->url($request->validated()['path'])
            ]
        );
    }
}
