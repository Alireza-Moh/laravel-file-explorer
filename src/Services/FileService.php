<?php

namespace Alireza\LaravelFileExplorer\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class FileService
{
    /**
     * Rename a file.
     *
     * @param string $diskName
     * @param array $validatedData Validated data
     *
     * @return array Result of the operation
     */
    public function rename(string $diskName, array $validatedData): array
    {
        $result = Storage::disk($diskName)->move($validatedData["oldPath"], $validatedData["newPath"]);

        return [
            "result" => [
                'status'  => $result ? "success" : "failed",
                'message' => $result ? "File renamed successfully" : "Failed to rename file",
            ]
        ];
    }

    /**
     * Delete a file.
     *
     * @param string $diskName
     * @param array $validatedData Validated data
     *
     * @return array Result of the operation
     */
    public function delete(string $diskName, array $validatedData): array
    {
        $itemToDelete = collect($validatedData["items"])->pluck("path")->toArray();

        $result = Storage::disk($diskName)->delete($itemToDelete);

        return [
            "result" => [
                'status'  => $result ? "success" : "failed",
                'message' => $result ? "File deleted successfully" : "Failed to delete file",
            ]
        ];
    }

    /**
     * Upload files.
     *
     * @param string $diskName
     * @param array $validatedData Validated data
     *
     * @return array Result of the operation
     */
    public function upload(string $diskName, array $validatedData): array
    {
        $storage = Storage::disk($diskName);
        $dirName = $validatedData["destination"];
        foreach ($validatedData["files"] as $file) {
            $fileName = $file->getClientOriginalName();

            if (!$storage->exists($dirName . '/' . $fileName) || (int)$validatedData["ifFileExist"] === 1) {
                $storage->putFileAs($dirName, $file, $fileName);
            }
        }

        return [
            "result" => [
                'status'  => "success",
                'message' => "File uploaded successfully",
                'items' => (new DirService())->getDirItems($diskName, $dirName),
            ]
        ];
    }

    /**
     * Download a file.
     *
     * @param string $diskName
     * @param array $validatedData Validated data
     *
     * @return StreamedResponse Streamed response for file download.
     */
    public function download(string $diskName, array $validatedData): StreamedResponse
    {
        return Storage::disk($diskName)->download($validatedData["files"][0]["path"]);
    }

    /**
     * Downloads files as a ZIP archive.
     *
     * @param string $diskName
     * @param array $validatedData Validated data
     *
     * @return BinaryFileResponse|JsonResponse
     */
    public function downloadAsZip(string $diskName, array $validatedData): BinaryFileResponse|JsonResponse
    {
        $zipFileName = $diskName . '_files.zip';
        $filteredFiles = array_filter($validatedData["files"], function($file) {
            return $file["type"] === "file";
        });

        $zipArchiveCreated = $this->createZipArchive($zipFileName, $diskName, $filteredFiles);
        if ($zipArchiveCreated) {
            return Response::download(storage_path($zipFileName))->deleteFileAfterSend();
        }

        return response()->json([
            "result" => [
                'status' => "failed",
                'message' => "Failed to download files",
            ]
        ]);
    }

    /**
     * Creates a ZIP archive.
     *
     * @param string $zipFileName
     * @param string $diskName
     * @param array $files Files to include in the ZIP archive.
     *
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
     * @param array $files Files to include in the ZIP archive.
     *
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
}
