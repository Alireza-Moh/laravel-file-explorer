<?php

namespace AlirezaMoh\LaravelFileExplorer\Supports\Traits;

use AlirezaMoh\LaravelFileExplorer\Events\ItemDeleted;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

trait DirManager
{
    protected function formatItemSize(float $size): string
    {
        $formattedSize = '-';
        if ($size > 0) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB');
            $i = floor(log($size, 1024));
            $formattedSize = number_format($size / pow(1024, $i), 2) . ' ' . $units[$i];
        }

        return $formattedSize;
    }

    protected function getLastModified(string $diskName, string $item): string
    {
        $lastModifiedTimestamp = Storage::disk($diskName)->lastModified($item);
        $lastModified = Carbon::createFromTimestamp($lastModifiedTimestamp)
            ->timezone(config('app.timezone'));

        return $lastModified->format(ConfigRepository::getModifiedFileTimeFormat());
    }

    protected function getDirItemsByType(string $diskName, string $type, bool $getFromDir = true, string $dirName = ''): array
    {
        $items = [];
        foreach ($this->getQuery($diskName, $dirName, $type, $getFromDir) as $item) {
            $items[] = $this->getMetaData($diskName, $dirName, $item, $type);
        }

        return $items;
    }

    private function getQuery(string $diskName, string $dirName, string $type, bool $getFromDir): mixed
    {
        $method = ($type === 'file') ? 'files' : 'directories';

        return $getFromDir ? Storage::disk($diskName)->$method($dirName) : Storage::disk($diskName)->$method();
    }

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
            'lastModified' => '-',
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

    private function fireDeleteEvent(string $diskName, array $item): void
    {
        event(new ItemDeleted($diskName, $item['name'], $item['path']));
    }
}
