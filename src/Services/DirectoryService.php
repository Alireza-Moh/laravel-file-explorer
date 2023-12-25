<?php

namespace Alireza\LaravelFileExplorer\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DirectoryService
{
    private Filesystem $storage;
    private string $diskName;


    public function __construct(string $diskName)
    {
        $this->storage = Storage::disk($diskName);
        $this->diskName = $diskName;
    }

    /**
     * Retrieve items within a directory.
     *
     * @param string $dirName The directory name.
     *
     * @return array items
     */
    public function getDirItems(string $dirName): array
    {
        $dirs = $this->getDirs($dirName);
        $folderContent = $this->storage->files($dirName);
        $items = [];

        foreach ($folderContent as $item) {
            $items[] = $this->getItemMetaData($item);
        }

        $this->getFoldersMetaData($dirs, $items);

        return $items;
    }

    /**
     * Get directories within the disk.
     *
     * @param string $dirName The directory name.
     *
     * @return array containing directories on the disk.
     */
    public function getDiskDirs(string $dirName = ''): array
    {
        $dirs = $this->getDirs($dirName);
        $allDirs = [];

        foreach ($dirs as $dir) {
            $subDirs = $this->getDiskDirs($dir);
            $allDirs[] = [
                "diskName" => $this->diskName,
                "label" => basename($dir),
                "subDir" => $subDirs
            ];
        }

        return $allDirs;
    }

    /**
     * Get files within the disk.
     *
     * @return array containing files on the disk.
     */
    public function getDiskFiles(): array
    {
        $folderContent = $this->storage->files();

        return array_map(function ($item) {
            return $this->getItemMetaData($item);
        }, $folderContent);
    }

    /**
     * Retrieve metadata for a specific item.
     *
     * @param string $item The item path.
     *
     * @return array Metadata information for the item.
     */
    private function getItemMetaData(string $item): array
    {
        return [
            'name' => $this->getItemBaseName($item),
            'size' => $this->getFileSizeInKB($item) . ' KB',
            'lastModified' => $this->getLastModified($item),
            'type' => 'file',
            "img" => "/vendor/laravel-file-explorer/img/file-earmark-fill.svg"
        ];
    }

    /**
     * Get a list of directories
     *
     * @param array $dirFromFolder directories.
     * @param array $items dir items.
     *
     * @return void
     */
    private function getFoldersMetaData(array $dirFromFolder, array &$items): void
    {
        foreach ($dirFromFolder as $dir) {
            $items[] = [
                'name' => $this->getItemBaseName($dir),
                'size' => "-",
                'lastModified' => "-",
                'type' => 'dir',
                "img" => "/vendor/laravel-file-explorer/img/folder-fill.svg"
            ];
        }
    }

    /**
     * Get the size of a file in kilobytes.
     *
     * @param string $item The file path.
     *
     * @return float Size
     */
    private function getFileSizeInKB(string $item): float
    {
        $fileSizeBytes = $this->storage->size($item);
        return round($fileSizeBytes / 1024, 2);
    }

    /**
     * Get the last modified time of an item.
     *
     * @param string $item The item path.
     * @param string $format The format of the timestamp (optional, default: 'Y-m-d H:i:s').
     *
     * @return string Last modified timestamp
     */
    private function getLastModified(string $item, string $format = 'Y-m-d H:i:s'): string
    {
        $lastModifiedTimestamp = $this->storage->lastModified($item);
        $lastModified = Carbon::createFromTimestamp($lastModifiedTimestamp);

        return $lastModified->format($format);
    }

    /**
     * Get the base name of an item.
     *
     * @param string $dir The item path.
     *
     * @return string base name
     */
    public function getItemBaseName(string $dir): string
    {
        return basename($dir);
    }

    /**
     * Get directories within a specified directory.
     *
     * @param string $dirName The directory name.
     *
     * @return array containing directories.
     */
    public function getDirs(string $dirName): array
    {
        return $this->storage->directories($dirName);
    }
}
