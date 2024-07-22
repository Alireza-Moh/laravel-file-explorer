<?php

namespace AlirezaMoh\LaravelFileExplorer\Services;

use AlirezaMoh\LaravelFileExplorer\Events\FileCreated;
use AlirezaMoh\LaravelFileExplorer\Events\ItemRenamed;
use AlirezaMoh\LaravelFileExplorer\Events\ItemsDownloaded;
use AlirezaMoh\LaravelFileExplorer\Events\ItemUploaded;
use AlirezaMoh\LaravelFileExplorer\Services\Contracts\ItemUtil;
use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use AlirezaMoh\LaravelFileExplorer\Supports\Download;
use AlirezaMoh\LaravelFileExplorer\Supports\Traits\DirManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemService implements ItemUtil
{
    use DirManager;

    public function rename(string $diskName, array $validatedData): JsonResponse
    {
        $result = Storage::disk($diskName)->move($validatedData['oldPath'], $validatedData['newPath']);
        $additionalData = [];

        if ($result) {
            event(new ItemRenamed($diskName, $validatedData));
            $additionalData['updatedItem'] = $this->getMetaData(
                $diskName,
                $validatedData['dirName'],
                $validatedData['newPath'],
                $validatedData['type']
            );
            return ApiResponse::success('Item renamed successfully', $additionalData);
        }

        return ApiResponse::error('Failed to rename item', $additionalData);
    }

    public function delete(string $diskName, array $validatedData): JsonResponse
    {
        $itemToDelete = collect($validatedData['items'])->pluck('path')->toArray();
        $result = Storage::disk($diskName)->delete($itemToDelete);

        if ($result) {
            foreach ($validatedData['items'] as $item) {
                $this->fireDeleteEvent($diskName, $item);
            }
            return ApiResponse::success('File deleted successfully');
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
                'items' => (new DirService())->getDirItems($diskName, $dirName)
            ]
        );
    }

    public function create(string $diskName, array $validatedData): JsonResponse
    {
        $destination = $validatedData['destination'];
        $result = Storage::disk($diskName)->put($validatedData['path'], '');

        $dirService = new DirService();
        $data =  [
            'items' => $dirService->getDirItems($diskName, $destination),
            'dirs' => $dirService->getDiskDirsForTree($diskName)
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
}
