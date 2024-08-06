<?php

namespace AlirezaMoh\LaravelFileExplorer\Services;

use AlirezaMoh\LaravelFileExplorer\Events\FileCreated;
use AlirezaMoh\LaravelFileExplorer\Events\ItemDeleted;
use AlirezaMoh\LaravelFileExplorer\Events\ItemRenamed;
use AlirezaMoh\LaravelFileExplorer\Events\ItemsDownloaded;
use AlirezaMoh\LaravelFileExplorer\Events\ItemUploaded;
use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use AlirezaMoh\LaravelFileExplorer\Supports\DiskManager;
use AlirezaMoh\LaravelFileExplorer\Supports\Download;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemService
{
    public function rename(string $diskName, array $validatedData): JsonResponse
    {
        Storage::disk($diskName)->move($validatedData['oldPath'], $validatedData['newPath']);
        $additionalData = [];

        event(new ItemRenamed($diskName, $validatedData));
        $diskManager = new DiskManager($diskName);
        $additionalData['updatedItem'] = $diskManager->createItem(
            $validatedData['type'],
            $validatedData['newPath'],
            $validatedData['parent']
        );

        return ApiResponse::success('Item renamed successfully', $additionalData);
    }

    public function delete(string $diskName, array $validatedData): JsonResponse
    {
        [$files, $dirs] = $this->sortByType($validatedData['items']);
        $filesToDelete = collect($files)->pluck('path')->toArray();

        $result = Storage::disk($diskName)->delete($filesToDelete);
        $this->deleteDirectories($diskName, $dirs);

        if ($result) {
            foreach ($files as $item) {
                $this->fireDeleteEvent($diskName, $item);
            }
            return ApiResponse::success('Items deleted successfully');
        }
        return ApiResponse::error('Failed to delete file');
    }

    public function upload(string $diskName, array $validatedData): JsonResponse
    {
        $storage = Storage::disk($diskName);
        $dirName = $validatedData['destination'] ?? "";
        foreach ($validatedData['items'] as $item) {
            $fileName = $this->getFileNameToUpload($item);

            $itemPath = $dirName
                ? ($dirName . '/' . $fileName)
                : $fileName;

            if (!$storage->exists($itemPath) || (int)$validatedData['ifItemExist'] === 1) {
                $result = $storage->putFileAs($dirName, $item, $fileName);

                if ($result) {
                    event(new ItemUploaded($diskName, $itemPath));
                }
            }
        }

        return ApiResponse::success(
            'Items uploaded successfully',
            [
                'items' => (new DiskManager($diskName))->getItemsByParentName($dirName, $dirName)
            ]
        );
    }

    public function create(string $diskName, array $validatedData): JsonResponse
    {
        try {
            $destination = $validatedData['destination'] ?? "";
            Storage::disk($diskName)->put($validatedData['path'], '');
            $diskManager = new DiskManager($diskName);
            $data =  [
                'items' => $diskManager->getItemsByParentName($destination, $destination),
                'dirs' => $diskManager->directories
            ];

            event(new FileCreated($diskName, $validatedData['path']));

            return ApiResponse::success('File created successfully', $data);
        } catch (Exception $exception) {
            return ApiResponse::success('Failed to create file');
        }
    }

    public function download(string $diskName, array $validatedData): BinaryFileResponse|StreamedResponse|JsonResponse
    {
        $items = $validatedData['items'];
        $downloadFactory = new Download($diskName, $items);
        event(new ItemsDownloaded($diskName, $items));

        return $downloadFactory->download();
    }

    public function getItemContent(string $diskName, array $validatedData): ?string
    {
        return Storage::disk($diskName)->get($validatedData['path']);
    }

    public function updateItemContent(string $diskName, array $validatedData): JsonResponse
    {
        try {
            Storage::disk($diskName)->put($validatedData['path'], $validatedData['item']);
            return ApiResponse::success('Content updated successfully');
        } catch (Exception $exception) {
            return ApiResponse::error('Failed to save changes');
        }
    }

    private function getFileNameToUpload($file): string
    {
        return ConfigRepository::hashFileWhenUploading()
            ? $file->hashName()
            : $file->getClientOriginalName();
    }

    private function deleteDirectories(string $diskName, array $dirs): void
    {
        $storage = Storage::disk($diskName);
        foreach ($dirs as $dir) {
            $result = $storage->deleteDirectory($dir['path']);
            if ($result) {
                $this->fireDeleteEvent($diskName, $dir);
            }
        }
    }

    private function sortByType(array $items): array
    {
        $files = [];
        $dirs = [];
        foreach ($items as $item) {
            if ($item['type'] === 'file') {
                $files[] = $item;
            } elseif ($item['type'] === 'dir') {
                $dirs[] = $item;
            }
        }

        return array($files, $dirs);
    }

    private function fireDeleteEvent(string $diskName, array $item): void
    {
        event(new ItemDeleted($diskName, $item['path']));
    }
}
