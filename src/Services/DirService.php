<?php
namespace AlirezaMoh\LaravelFileExplorer\Services;

use AlirezaMoh\LaravelFileExplorer\Events\DirCreated;
use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\DiskManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DirService
{
    public function create(string $diskName, array $validatedData): JsonResponse
    {
        $destination = $validatedData['destination'];
        $path = $validatedData['path'];
        $result = Storage::disk($diskName)->makeDirectory($path);

        $diskManager = new DiskManager($diskName);
        $data =  [
            'items' => $diskManager->getItemsByDirectoryName($destination, $destination),
            'dirs' => $diskManager->directories
        ];

        if ($result) {
            event(new DirCreated($diskName, $destination, $path));
            return ApiResponse::success('Directory created successfully', $data);
        }
        return ApiResponse::error('Failed to create directory', $data);
    }
}
