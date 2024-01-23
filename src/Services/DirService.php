<?php
namespace Alireza\LaravelFileExplorer\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DirService
{
    /**
     * Retrieve items within a directory.
     *
     * @param string $diskName
     * @param string $dirName
     *
     * @return array items
     */
    public function getDirItems(string $diskName, string $dirName): array
    {
        $items = [];

        foreach (Storage::disk($diskName)->files($dirName) as $item) {
            $items[] = $this->getItemMetaData($diskName, $dirName, $item);
        }

        foreach ($this->getDirs($diskName, $dirName) as $dir) {
            $items[] = $this->getFolderMetaData($diskName, $dirName, $dir);
        }

        return $items;
    }

    /**
     * Get directories within the disk.
     *
     * @param string $diskName
     * @param string $dirName
     *
     * @return array containing directories on the disk.
     */
    public function getDiskDirs(string $diskName, string $dirName = ''): array
    {
        return $this->getDiskItems($diskName, $dirName, true);
    }

    /**
     * Get files within the disk.
     *
     * @param string $diskName
     * @param string $dirName
     *
     * @return array containing files on the disk.
     */
    public function getDiskFiles(string $diskName, string $dirName): array
    {
        return $this->getDiskItems($diskName, $dirName, false);
    }

    /**
     * Find a directory by label.
     *
     * @param string $diskName
     * @param string $dirName
     * @param array $dirs
     *
     * @return array|null The found directory or null if not found.
     */
    public function findDirectoryByName(string $diskName, string $dirName, array $dirs = []): ?array
    {
        if (empty($dirs)) {
            $dirs = $this->getDiskDirs($diskName);
        }

        foreach ($dirs as $directory) {
            if ($directory['name'] === $dirName) {
                return $directory;
            }

            if (!empty($directory['subDir'])) {
                $foundInSubDir = $this->findDirectoryByName($dirName, $directory['subDir']);
                if ($foundInSubDir !== null) {
                    return $foundInSubDir;
                }
            }
        }

        return null;
    }

    /**
     * Delete a directory.
     *
     * @param string $diskName
     * @param array $validatedData dirs to delete
     *
     * @return array
     */
    public function delete(string $diskName, array $validatedData): array
    {
        if ($this->existDefaultDirOnLoadingInArray($validatedData["items"])) {
            return [
                "status" => "failed",
                "message" => "You cannot delete the default directory because it's needed for initiation"
            ];
        }
        $storage = Storage::disk($diskName);
        foreach ($validatedData["items"] as $dir) {
            $storage->deleteDirectory($dir["path"]);
        }

        return [
            "status" => "success",
            "message" => 'Directory deleted successfully'
        ];
    }


    private function getDiskItems(string $diskName, string $dirName, bool $isDir): array
    {
        $items = $isDir ? $this->getDirs($diskName, $dirName) : Storage::disk($diskName)->files($dirName);

        return array_map(function ($item) use ($diskName, $dirName, $isDir) {
            return $isDir ? $this->getFolderMetaData($diskName, $dirName, $item) : $this->getItemMetaData($diskName, $dirName, $item);
        }, $items);
    }

    /**
     * Get directories within a specified directory.
     *
     * @param string $diskName
     * @param string $dirName
     *
     * @return array containing directories.
     */
    private function getDirs(string $diskName, string $dirName): array
    {
        return Storage::disk($diskName)->directories($dirName);
    }

    /**
     * Check if the default directory from default disk is present in the given array of directories.
     *
     * @param array $dirs An array containing directory information.
     *
     * @return bool
     */
    private function existDefaultDirOnLoadingInArray(array $dirs): bool
    {
        $defaultDirOnLoading = config('laravel-file-explorer.default_directory_from_default_disk_on_loading');

        if ($defaultDirOnLoading === null) {
            return false;
        }
        $filteredDirs = array_filter($dirs, function ($dir) use ($defaultDirOnLoading) {
            return $dir['name'] === $defaultDirOnLoading;
        });

        return !empty($filteredDirs);
    }

    private function getItemMetaData(string $diskName, string $dirName, string $item): array
    {
        $url = Storage::disk($diskName)->url($item);
        return [
            'diskName' => $diskName,
            'dirName' => $dirName,
            'name' => $this->getItemBaseName($item),
            'size' => $this->getFileSizeInKB($diskName, $item),
            'lastModified' => $this->getLastModified($diskName, $item),
            'type' => 'file',
            'path' => $item,
            "url" => $url,
            "extension" => pathinfo($item, PATHINFO_EXTENSION),
        ];
    }

    private function getFolderMetaData(string $diskName, string $dirName, string $dir): array
    {
        return [
            'diskName' => $diskName,
            'dirName' => $dirName,
            'name' => $this->getItemBaseName($dir),
            'size' => "-",
            'lastModified' => "-",
            'type' => 'dir',
            'path' => $dir,
            "extension" => null
        ];
    }

    private function getItemBaseName(string $dir): string
    {
        return basename($dir);
    }

    private function getLastModified(string $diskName, string $item, string $format = 'Y-m-d H:i:s'): string
    {
        $lastModifiedTimestamp = Storage::disk($diskName)->lastModified($item);
        $lastModified = Carbon::createFromTimestamp($lastModifiedTimestamp)->timezone('Europe/Vienna');

        return $lastModified->format($format);
    }

    private function getFileSizeInKB(string $diskName, string $item): float
    {
        $fileSizeBytes = Storage::disk($diskName)->size($item);
        return round($fileSizeBytes / 1024, 2);
    }
}
