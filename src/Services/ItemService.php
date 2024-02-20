<?php

namespace Alireza\LaravelFileExplorer\Services;

use Alireza\LaravelFileExplorer\Events\FileCreated;
use Alireza\LaravelFileExplorer\Events\ItemRenamed;
use Alireza\LaravelFileExplorer\Events\ItemUploaded;
use Alireza\LaravelFileExplorer\Services\Contracts\ItemOperations;
use Alireza\LaravelFileExplorer\Services\Supports\Downloader;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemService extends BaseItemManager implements ItemOperations
{
    /**
     * Rename a file.
     *
     * @param string $diskName
     * @param string $oldName
     * @param array $validatedData
     * @return array
     */
    public function rename(string $diskName, string $oldName, array $validatedData): array
    {
        $result = Storage::disk($diskName)->move($validatedData["oldPath"], $validatedData["newPath"]);

        if ($result) {
            event(new ItemRenamed($diskName, $oldName, $validatedData));
        }
        return $this->getResponse($result, "Item renamed successfully", "Failed to rename item");
    }

    /**
     * Delete a file.
     *
     * @param string $diskName
     * @param array $validatedData
     * @return array
     */
    public function delete(string $diskName, array $validatedData): array
    {
        $itemToDelete = collect($validatedData["items"])->pluck("path")->toArray();
        $result = Storage::disk($diskName)->delete($itemToDelete);

        if ($result) {
            foreach ($validatedData["items"] as $item) {
                $this->fireDeleteEvent($diskName, $item);
            }
        }
        return $this->getResponse($result, "File deleted successfully", "Failed to delete file");
    }

    /**
     * Upload files.
     *
     * @param string $diskName
     * @param array $validatedData
     * @return array
     */
    public function upload(string $diskName, array $validatedData): array
    {
        $storage = Storage::disk($diskName);
        $dirName = $validatedData["destination"];
        foreach ($validatedData["items"] as $item) {
            $fileName = $this->getFileNameToUpload($item);

            $itemPath = $dirName . '/' . $fileName;
            if (!$storage->exists($itemPath) || (int)$validatedData["ifItemExist"] === 1) {
                $result = $storage->putFileAs($dirName, $item, $fileName);

                if ($result) {
                    event(new ItemUploaded($diskName, $dirName, $fileName, $itemPath));
                }
            }
        }

        return $this->getResponse(
            true,
            success: "Items uploaded successfully",
            additionalData: [
                'items' => (new DirService())->getDirItems($diskName, $dirName)
            ]
        );
    }

    /**
     * Create a file
     *
     * @param string $diskName
     * @param array $validatedData
     * @return array
     */
    public function create(string $diskName, array $validatedData): array
    {
        $filePath = $validatedData["path"];
        $destination =$validatedData["destination"];
        $result = Storage::disk($diskName)->put($filePath, "");
        $message = $result ? "File created successfully" : "Failed to create file";

        if ($result) {
            event(new FileCreated($diskName, $destination, $filePath));
        }

        return $this->getCreationResponse($diskName, $result, $message, $destination);
    }

    /**
     * Download item/items
     *
     * @param string $diskName
     * @param array $validatedData
     * @return BinaryFileResponse|JsonResponse|StreamedResponse
     * @throws Exception
     */
    public function download(string $diskName, array $validatedData): BinaryFileResponse|StreamedResponse|JsonResponse
    {
        $downloadFactory = new Downloader($diskName, $validatedData["items"]);
        return $downloadFactory->download();
    }

    /**
     * Get item content
     *
     * @param string $diskName
     * @param string $path
     * @return string|null
     */
    public function getItemContent(string $diskName, string $path): ?string
    {
        return Storage::disk($diskName)->get($path);
    }

    /**
     * Update item content
     *
     * @param string $diskName
     * @param array $validatedData
     * @return array
     */
    public function updateItemContent(string $diskName, array $validatedData): array
    {
        $result = Storage::disk($diskName)->put($validatedData["path"], $validatedData["item"]->get());

        return $this->getResponse($result, "Changes saved successfully", "Failed to save chnages");
    }

    /**
     * Get file name to upload
     *
     * @param $file
     * @return string
     */
    private function getFileNameToUpload($file): string
    {
        if (ConfigRepository::getHashFileWhenUploading()) {
            return $file->hashName();
        }
        return $file->getClientOriginalName();
    }
}
