<?php
namespace AlirezaMoh\LaravelFileExplorer\Services;

use AlirezaMoh\LaravelFileExplorer\Events\DirCreated;
use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\DiskManager;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DirService
{
    public function create(string $diskName, array $validatedData): JsonResponse
    {
        try {
            $destination = $validatedData['destination'] ?? "";
            $path = $validatedData['path'];
            Storage::disk($diskName)->makeDirectory($path);

            $diskManager = new DiskManager($diskName);
            $data =  [
                'items' => $diskManager->getItemsByParentName($destination, $destination),
                'dirs' => $diskManager->directories
            ];

            event(new DirCreated($diskName, $path));
            return ApiResponse::success('Directory created successfully', $data);
        } catch (Exception $exception) {
            return ApiResponse::error('Failed to create directory');
        }
    }
}
