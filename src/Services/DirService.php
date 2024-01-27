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
            $items[] = $this->getMetaData($diskName, $dirName, $item, "file");
        }

        foreach ($this->getDirs($diskName, $dirName) as $dir) {
            $items[] = $this->getMetaData($diskName, $dirName, $dir, "dir");
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
        $dirs = $this->getDirs($diskName, $dirName);
        $allDirs = [];

        foreach ($dirs as $dir) {
            $subDirs = $this->getDiskDirs($diskName, $dir);
            $allDirs[] = [
                "diskName" => $diskName,
                "name" => basename($dir),
                "path" => $dir,
                "type" => "dir",
                "subDir" => $subDirs,
            ];
        }
        return $allDirs;
    }

    /**
     * Get files within the disk.
     *
     * @param string $diskName
     * @param string $dirName
     * @return array
     */
    public function getDiskFiles(string $diskName, string $dirName): array
    {
        $dirContent = Storage::disk($diskName)->files();

        return array_map(function ($item) use ($diskName, $dirName) {
            return $this->getMetaData($diskName, $dirName, $item, "file");
        }, $dirContent);
    }

    /**
     * Find a directory by name.
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
                $foundInSubDir = $this->findDirectoryByName($diskName, $dirName, $directory['subDir']);
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

    /**
     * Retrieve metadata for a specific item.
     *
     * @param string $diskName
     * @param string $dirName
     * @param string $path
     * @param string $type
     * @return array Metadata information for the item.
     */
    private function getMetaData(string $diskName, string $dirName, string $path, string $type): array
    {
        $url = Storage::disk($diskName)->url($path);
        $commonMetaData = [
            'diskName' => $diskName,
            'dirName' => $dirName,
            'name' => basename($path),
            'path' => $path,
            'type' => $type,
            'size' => "-",
            'lastModified' => "-",
            'extension' => null,
            'url' => $url,
        ];

        if ($type === 'file') {
            $commonMetaData['size'] = $this->getFileSizeInKB($diskName, $path);
            $commonMetaData['lastModified'] = $this->getLastModified($diskName, $path);
            $commonMetaData['extension'] = pathinfo($path, PATHINFO_EXTENSION);
        }

        return $commonMetaData;
    }

    /**
     * Get the size of a file in kilobytes.
     *
     * @param string $diskName
     * @param string $item The file path.
     *
     * @return float Size
     */
    private function getFileSizeInKB(string $diskName, string $item): float
    {
        $fileSizeBytes = Storage::disk($diskName)->size($item);
        return round($fileSizeBytes / 1024, 2);
    }

    /**
     * Get the last modified time of an item.
     *
     * @param string $diskName
     * @param string $item The item path.
     * @param string $format
     *
     * @return string
     */
    private function getLastModified(string $diskName, string $item, string $format = 'Y-m-d H:i:s'): string
    {
        $lastModifiedTimestamp = Storage::disk($diskName)->lastModified($item);
        $lastModified = Carbon::createFromTimestamp($lastModifiedTimestamp)->timezone('Europe/Vienna');

        return $lastModified->format($format);
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
}
