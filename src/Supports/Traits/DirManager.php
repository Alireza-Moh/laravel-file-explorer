<?php

namespace Alireza\LaravelFileExplorer\Supports\Traits;

use Alireza\LaravelFileExplorer\Services\ConfigRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

trait DirManager
{
    /**
     * Format file size
     *
     * @param float $size
     * @return string
     */
    protected function formatItemSize(float $size): string
    {
        $formattedSize = "-";
        if ($size > 0) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB');
            $i = floor(log($size, 1024));
            $formattedSize = number_format($size / pow(1024, $i), 2) . ' ' . $units[$i];
        }

        return $formattedSize;
    }

    /**
     * Get the last modified time of an item.
     *
     * @param string $diskName
     * @param string $item
     * @return string
     */
    protected function getLastModified(string $diskName, string $item): string
    {
        $lastModifiedTimestamp = Storage::disk($diskName)->lastModified($item);
        $lastModified = Carbon::createFromTimestamp($lastModifiedTimestamp)
            ->timezone(ConfigRepository::getTimezone());

        return $lastModified->format(self::CARBON_TIME_FORMAT);
    }

    /**
     * Retrieves the metadata for items of a specific type (file or dir)
     * within a given directory on a specified disk.
     *
     * @param string $diskName
     * @param string $type
     * @param bool $getFromDir
     * @param string $dirName
     * @return array
     */
    protected function getDirItemsByType(string $diskName, string $type, bool $getFromDir = true, string $dirName = ""): array
    {
        $items = [];
        foreach ($this->getQuery($diskName, $dirName, $type, $getFromDir) as $item) {
            $items[] = $this->getMetaData($diskName, $dirName, $item, $type);
        }

        return $items;
    }

    /**
     * Generates a query fpr retrieving items
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

    /**
     * Retrieve metadata for a specific item.
     *
     * @param string $diskName
     * @param string $dirName
     * @param string $path
     * @param string $type
     * @return array
     */
    private function getMetaData(string $diskName, string $dirName, string $path, string $type): array
    {
        $storage = Storage::disk($diskName);
        $url = $storage->url($path);
        $commonMetaData = [
            'diskName' => $diskName,
            'dirName' => $dirName,
            'name' => basename($path),
            'path' => $path,
            'type' => $type,
            'lastModified' => "-",
            'extension' => null,
            'url' => $url,
            'isChecked' => false,
        ];

        if ($type === 'file') {
            $fileSize = $storage->size($path);
            $commonMetaData['size'] = ($fileSize === 0) ? $fileSize : $this->formatItemSize($storage->size($path));
            $commonMetaData['lastModified'] = $this->getLastModified($diskName, $path);
            $commonMetaData['extension'] = pathinfo($path, PATHINFO_EXTENSION);
        }

        if ($type === 'dir') {
            $size = 0;
            foreach ($storage->allFiles($path) as $filePath) {
                $size += $storage->size($filePath);
            }
            $commonMetaData['size'] = $this->formatItemSize($size);
        }

        return $commonMetaData;
    }
}
