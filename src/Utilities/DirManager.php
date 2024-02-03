<?php

namespace Alireza\LaravelFileExplorer\Utilities;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

trait DirManager
{
    /**
     * Check if the directory is the default directory on loading.
     *
     * @param string|null $defaultDirOnLoading
     * @param string $dirName
     * @return bool
     */
    protected function isDefaultDirectory(?string $defaultDirOnLoading, string $dirName): bool
    {
        return $defaultDirOnLoading && $defaultDirOnLoading === $dirName;
    }

    /**
     * Get the size of a file in kilobytes.
     *
     * @param string $diskName
     * @param string $item
     * @return float
     */
    protected function getFileSizeInKB(string $diskName, string $item): float
    {
        $fileSizeBytes = Storage::disk($diskName)->size($item);
        return round($fileSizeBytes / 1024, 2);
    }

    /**
     * Get the last modified time of an item.
     *
     * @param string $diskName
     * @param string $item
     * @param string $format
     * @return string
     */
    protected function getLastModified(string $diskName, string $item, string $format = 'Y-m-d H:i:s'): string
    {
        $lastModifiedTimestamp = Storage::disk($diskName)->lastModified($item);
        $lastModified = Carbon::createFromTimestamp($lastModifiedTimestamp)->timezone('Europe/Vienna');

        return $lastModified->format($format);
    }

    /**
     * Check if the default directory from default disk is present in the given array of directories.
     *
     * @param array $dirs
     * @return bool
     */
    protected function existDefaultDirOnLoadingInArray(array $dirs): bool
    {
        $defaultDirOnLoading = config('laravel-file-explorer.default_directory_on_loading');

        if ($defaultDirOnLoading === null) {
            return false;
        }
        $filteredDirs = array_filter($dirs, function ($dir) use ($defaultDirOnLoading) {
            return $dir['name'] === $defaultDirOnLoading;
        });

        return !empty($filteredDirs);
    }

    /**
     * Retrieves the metadata for items of a specific type (file or directory) within a given directory on a specified disk.
     *
     * @param string $diskName
     * @param string $type
     * @param bool $getFromDir
     * @param string $dirName
     * @return array
     */
    protected function getDirItemsByType(string $diskName, string $type, bool $getFromDir = true, string $dirName = "",): array
    {
        $items = [];
        foreach ($this->getQuery($diskName, $dirName, $type, $getFromDir) as $item) {
            $items[] = $this->getMetaData($diskName, $item, $type);
        }

        return $items;
    }

    /**
     * Generates and retrieves a query result for items of a specific type (file or directory) on a specified disk.
     *
     * @param string $diskName
     * @param string $dirName
     * @param string $type
     * @param bool $getFromDir
     * @return mixed
     */
    private function getQuery(string $diskName, string $dirName, string $type, bool $getFromDir): mixed
    {
        $method = ($type === 'file') ? 'files' : 'directories';

        return $getFromDir ? Storage::disk($diskName)->$method($dirName) : Storage::disk($diskName)->$method();
    }
}
