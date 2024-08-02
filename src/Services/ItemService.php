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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemService
{
    public function rename(string $diskName, array $validatedData): JsonResponse
    {
        $result = Storage::disk($diskName)->move($validatedData['oldPath'], $validatedData['newPath']);
        $additionalData = [];
        if ($result) {
            event(new ItemRenamed($diskName, $validatedData));
            $diskManager = new DiskManager($diskName);
            $additionalData['updatedItem'] = $diskManager->createItem(
                $validatedData['type'],
                $validatedData['newPath'],
                $validatedData['parent']
            );
            return ApiResponse::success('Item renamed successfully', $additionalData);
        }

        return ApiResponse::error('Failed to rename item', $additionalData);
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
        $dirName = $validatedData['destination'];
        foreach ($validatedData['items'] as $item) {
            $fileName = $this->getFileNameToUpload($item);

            $itemPath = $dirName . '/' . $fileName;
            if (!$storage->exists($itemPath) || (int)$validatedData['ifItemExist'] === 1) {
                $result = $storage->putFileAs($dirName, $item, $fileName);

                if ($result) {
                    event(new ItemUploaded($diskName, $dirName, $fileName, $itemPath));
                }
            }
        }

        return ApiResponse::success(
            'Items uploaded successfully',
            [
                'items' => (new DiskManager($diskName))->getItemsByDirectoryName($dirName, $dirName)
            ]
        );
    }

    public function create(string $diskName, array $validatedData): JsonResponse
    {
        $destination = $validatedData['destination'];
        $result = Storage::disk($diskName)->put($validatedData['path'], '');
        $diskManager = new DiskManager($diskName);
        $data =  [
            'items' => $diskManager->getItemsByDirectoryName($destination, $destination),
            'dirs' => $diskManager->directories
        ];

        if ($result) {
            event(new FileCreated($diskName, $destination, $validatedData['destination']));
            return ApiResponse::success('File created successfully', $data);
        }
        return ApiResponse::success('Failed to create file', $data);
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
        $result = Storage::disk($diskName)->put($validatedData['path'], $validatedData['item']->get());

        if ($result) {
            return ApiResponse::success('Changes saved successfully');
        }
        return ApiResponse::error('Failed to save changes');
    }

    private function getFileNameToUpload($file): string
    {
        return ConfigRepository::hashFileWhenUploading() ? $file->hashName() : $file->getClientOriginalName();
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
        event(new ItemDeleted($diskName, $item['name'], $item['path']));
    }
}
