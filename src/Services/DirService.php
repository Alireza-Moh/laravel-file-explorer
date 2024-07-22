<?php
namespace AlirezaMoh\LaravelFileExplorer\Services;

use AlirezaMoh\LaravelFileExplorer\Events\DirCreated;
use AlirezaMoh\LaravelFileExplorer\Services\Contracts\ItemUtil;
use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\Traits\DirManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DirService implements ItemUtil
{
    use DirManager;

    public function getDirItems(string $diskName, string $dirName): array
    {
        return array_merge(
            $this->getDirItemsByType($diskName, 'file', dirName: $dirName),
            $this->getDirItemsByType($diskName, 'dir', dirName: $dirName)
        );
    }

    public function getDiskDirsForTree(string $diskName, string $dirName = ''): array
    {
        $dirs = Storage::disk($diskName)->directories($dirName);
        $allDirs = [];

        foreach ($dirs as $dir) {
            $subDirs = $this->getDiskDirsForTree($diskName, $dir);
            $allDirs[] = [
                'diskName' => $diskName,
                'dirName' => $dirName,
                'name' => basename($dir),
                'path' => $dir,
                'type' => 'dir',
                'subDir' => $subDirs,
            ];
        }
        return $allDirs;
    }

    public function getDiskItems(string $diskName): array
    {
        return array_merge(
            $this->getDirItemsByType($diskName, 'file', false),
            $this->getDirItemsByType($diskName, 'dir', false)
        );
    }

    public function findDirectoryByName(string $diskName, string $dirName, array $dirs = []): ?array
    {
        if (empty($dirs)) {
            $dirs = $this->getDiskDirsForTree($diskName);
        }

        foreach ($dirs as $directory) {
            if ($directory['name'] === $dirName) {
                return $directory;
            }

            if (!empty($directory['subDir'])) {
                $foundInSubDir = $this->findDirectoryByName($diskName, $dirName, $directory['subDir']);
                if ($foundInSubDir !== null) {
                    return $foundInSubDir;
                }
            }
        }
        return null;
    }

    public function delete(string $diskName, array $validatedData): JsonResponse
    {
        $storage = Storage::disk($diskName);
        foreach ($validatedData['items'] as $dir) {
            $result = $storage->deleteDirectory($dir['path']);
            if ($result) {
                $this->fireDeleteEvent($diskName, $dir);
            }
        }

        return ApiResponse::success('Directory deleted successfully');
    }

    public function create(string $diskName, array $validatedData): JsonResponse
    {
        $result = Storage::disk($diskName)->makeDirectory($validatedData['path']);
        $data =  [
            'items' => $this->getDirItems($diskName, $validatedData['destination']),
            'dirs' => $this->getDiskDirsForTree($diskName)
        ];

        if ($result) {
            event(new DirCreated($diskName, $validatedData['destination'], $validatedData['path']));
            return ApiResponse::success('Directory created successfully', $data);
        }

        return ApiResponse::error('Failed to create directory', $data);
    }
}
