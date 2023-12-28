<?php

namespace Alireza\LaravelFileExplorer\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DirService
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
            $items[] = $this->getItemMetaData($dirName, $item);
        }

        $this->getFoldersMetaData($dirName, $dirs, $items);

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
                "path" => $dir,
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
    public function getDiskFiles(string $dirName): array
    {
        $folderContent = $this->storage->files();

        return array_map(function ($dirName, $item) {
            return $this->getItemMetaData($dirName, $item);
        }, $folderContent);
    }

    /**
     * Retrieve metadata for a specific item.
     *
     * @param string $dirName The directory name.
     * @param string $item The item path.
     *
     * @return array Metadata information for the item.
     */
    private function getItemMetaData(string $dirName, string $item): array
    {
        return [
            'diskName' => $this->diskName,
            'dirName' => $dirName,
            'name' => $this->getItemBaseName($item),
            'size' => $this->getFileSizeInKB($item) . ' KB',
            'lastModified' => $this->getLastModified($item),
            'type' => 'file',
            'path' => $item,
            "url" => $this->storage->url($item),
        ];
    }

    /**
     * Get a list of directories
     *
     * @param string $dirName The directory name.
     * @param array $dirFromFolder directories.
     * @param array $items dir items.
     *
     * @return void
     */
    private function getFoldersMetaData(string $dirName, array $dirFromFolder, array &$items): void
    {
        foreach ($dirFromFolder as $dir) {
            $items[] = [
                'diskName' => $this->diskName,
                'dirName' => $dirName,
                'name' => $this->getItemBaseName($dir),
                'size' => "-",
                'lastModified' => "-",
                'type' => 'dir',
                'path' => $dir,
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
        $lastModified = Carbon::createFromTimestamp($lastModifiedTimestamp)->timezone('Europe/Vienna');

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
