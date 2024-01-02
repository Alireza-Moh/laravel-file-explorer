<?php

namespace Alireza\LaravelFileExplorer\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileService
{
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

    public function delete(string $diskName, array $validatedData): array
    {
        $result = Storage::disk($diskName)->delete($validatedData["path"]);

        return [
            "result" => [
                'status'  => $result ? "success" : "failed",
                'message' => $result ? "File deleted successfully" : "Failed to delete file",
            ]
        ];
    }

    public function upload(string $diskName, array $validatedData): array
    {
        $result = null;
        $storage = Storage::disk($diskName);
        $dirName = $validatedData["destination"];
        foreach ($validatedData["files"] as $file) {
            $fileName = $file->getClientOriginalName();

            if ($storage->exists($dirName . '/' . $fileName)) {
                if ((int) $validatedData["ifFileExist"] === 1) { //overwrite file if exists
                    $result = $storage->putFileAs($dirName, $file, $fileName);
                }
                else {
                    $result = "skipped";
                }
            }
            else {
                $result = $storage->putFileAs($dirName, $file, $fileName);
            }
        }

        $dirService = new DirService($diskName);

        return [
            "result" => [
                'status'  => $result ? "success" : "failed",
                'message' => $result ? "File uploaded successfully" : "Failed to upload file",
                'items' => $dirService->getDirItems($dirName),
            ]
        ];
    }

    public function download(string $diskName, array $validatedData): StreamedResponse
    {
        return Storage::disk($diskName)->download($validatedData["path"]);
    }
}
