<?php

namespace Alireza\LaravelFileExplorer\Services;

use Alireza\LaravelFileExplorer\Services\Contracts\BaseStorage;
use Illuminate\Support\Facades\Storage;

class FileService implements BaseStorage
{
    public function create(string $diskName, array $validatedData)
    {
        // TODO: Implement create() method.
    }

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

    public function update(string $diskName, array $validatedData)
    {
        // TODO: Implement update() method.
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

    public function get(string $diskName, array $validatedData)
    {
        // TODO: Implement get() method.
    }
}
