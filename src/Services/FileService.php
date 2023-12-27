<?php

namespace Alireza\LaravelFileExplorer\Services;

use Illuminate\Support\Facades\Storage;

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
}
