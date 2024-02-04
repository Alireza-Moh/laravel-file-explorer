<?php

namespace Alireza\LaravelFileExplorer\Services;

use Alireza\LaravelFileExplorer\Events\FileCreated;
use Alireza\LaravelFileExplorer\Events\ItemRenamed;
use Alireza\LaravelFileExplorer\Events\ItemUploaded;
use Alireza\LaravelFileExplorer\Services\Contracts\ItemOperations;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

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
            if (!$storage->exists($itemPath) || (int)$validatedData["ifFileExist"] === 1) {
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
        $result = Storage::disk($diskName)->put($validatedData["path"], "");
        $message = $result ? "File created successfully" : "Failed to create file";

        if ($result) {
            event(new FileCreated($diskName, $validatedData["destination"], $validatedData["path"]));
        }

        return $this->getCreationResponse($diskName, $result, $message, $validatedData["destination"]);
    }

    /**
     * Download a file.
     *
     * @param string $diskName
     * @param array $validatedData
     * @return StreamedResponse
     */
    public function download(string $diskName, array $validatedData): StreamedResponse
    {
        return Storage::disk($diskName)->download($validatedData["items"][0]["path"]);
    }

    /**
     * Downloads files as a ZIP archive.
     *
     * @param string $diskName
     * @param array $validatedData
     * @return BinaryFileResponse|JsonResponse
     */
    public function downloadAsZip(string $diskName, array $validatedData): BinaryFileResponse|JsonResponse
    {
        $zipFileName = $diskName . '_files.zip';
        $filteredFiles = array_filter($validatedData["items"], function($file) {
            return $file["type"] === "file";
        });

        $zipArchiveCreated = $this->createZipArchive($zipFileName, $diskName, $filteredFiles);
        if ($zipArchiveCreated) {
            return Response::download(storage_path($zipFileName))->deleteFileAfterSend();
        }
        return response()->json(
            $this->getResponse(false, failure: "Failed to download items")
        );
    }

    /**
     * Creates a ZIP archive.
     *
     * @param string $zipFileName
     * @param string $diskName
     * @param array $files
     * @return bool
     */
    private function createZipArchive(string $zipFileName, string $diskName, array $files): bool
    {
        $zip = new ZipArchive;
        if ($zip->open(storage_path($zipFileName), ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $this->addFilesToZip($zip, $diskName, $files);
            $zip->close();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Adds files to ZIP archive.
     *
     * @param ZipArchive $zip
     * @param string $diskName
     * @param array $files
     * @return void
     */
    private function addFilesToZip(ZipArchive $zip, string $diskName, array $files): void
    {
        $storage = Storage::disk($diskName);

        foreach ($files as $file) {
            $filePath = $file["path"];
            if ($storage->exists($filePath)) {
                $fileContent = $storage->get($filePath);
                $zip->addFromString(basename($filePath), $fileContent);
            }
        }
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
