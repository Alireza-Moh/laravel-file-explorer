<?php

namespace AlirezaMoh\LaravelFileExplorer\Services;

use AlirezaMoh\LaravelFileExplorer\Events\FileCreated;
use AlirezaMoh\LaravelFileExplorer\Events\ItemRenamed;
use AlirezaMoh\LaravelFileExplorer\Events\ItemsDownloaded;
use AlirezaMoh\LaravelFileExplorer\Events\ItemUploaded;
use AlirezaMoh\LaravelFileExplorer\Services\Contracts\ItemUtil;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use AlirezaMoh\LaravelFileExplorer\Supports\Download;
use AlirezaMoh\LaravelFileExplorer\Supports\Traits\DirManager;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemService extends BaseItemManager implements ItemUtil
{
    use DirManager;

    /**
     * Rename a file.
     *
     * @param string $diskName
     * @param array $validatedData
     * @return array
     */
    public function rename(string $diskName, array $validatedData): array
    {
        $result = Storage::disk($diskName)->move($validatedData["oldPath"], $validatedData["newPath"]);

        $additionalData = [];
        if ($result) {
            event(new ItemRenamed($diskName, $validatedData));
            $additionalData["updatedItem"] = $this->getMetaData(
                $diskName,
                $validatedData["dirName"],
                $validatedData["newPath"],
                $validatedData["type"]
            );
        }
        return $this->getResponse($result, "Item renamed successfully", "Failed to rename item", $additionalData);
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
        $items = $validatedData["items"];
        $downloadFactory = new Download($diskName, $items);
        event(new ItemsDownloaded($diskName, $items));

        return $downloadFactory->download();
    }

    /**
     * Get item content
     *
     * @param string $diskName
     * @param array $validatedData
     * @return string|null
     */
    public function getItemContent(string $diskName, array $validatedData): ?string
    {
        return Storage::disk($diskName)->get($validatedData["path"]);
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
