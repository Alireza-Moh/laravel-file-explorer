<?php

namespace AlirezaMoh\LaravelFileExplorer\Supports;

use AlirezaMoh\LaravelFileExplorer\Supports\Traits\FormatAbleSize;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DiskManager
{
    use FormatAbleSize;

    public Collection $all;

    public Collection $directories;

    public Collection $diskFiles;

    private const DIRECTORY_TYPE = 'dir';

    private const FILE_TYPE = 'file';

    private Filesystem $storage;

    private string $diskName;

    public function __construct(string $diskName)
    {
        $this->diskName = $diskName;
        $this->storage = Storage::disk($diskName);

        $this->all = collect();
        $this->directories = collect();
        $this->diskFiles = collect();
        $this->setUpDirectories();
        $this->setUpFiles();
    }

    public function findDirectoryByName(string $directoryName): Item|null
    {
        return $this->searchRecursiveForDirectory($this->directories, $directoryName);
    }

    public function getItemsByParentName(string $directoryName, string $directoryPath): array
    {
        $directories = $this->findByParentName($this->all, $directoryName);
        $files = $this->setUpItems($this->storage->files($directoryPath), self::FILE_TYPE, $directoryName);

        return $directories->merge($files)->toArray();
    }

    public function createItem(mixed $type, mixed $item, string $parentName): Item
    {
        $size = $type === self::DIRECTORY_TYPE
            ? $this->getDirectorySize($item)
            : $this->getFileSize($item);

        $subDirectories = collect();
        if ($type === self::DIRECTORY_TYPE) {
            $subDirectories = $this->setUpItems($this->storage->directories($item), $type, basename($item), true);
        }

        return new Item(
            $this->diskName,
            $parentName,
            basename($item),
            $item,
            $type,
            $size,
            $this->formatSize($size),
            $this->getFileUrl($type, $item),
            $this->getFileExtension($type, $item),
            false,
            $this->getLastModified($item, $type),
            $subDirectories
        );
    }

    private function setUpItems(array $items, string $type, string $parentName = "", bool $searchingForSubDirs = false): Collection
    {
        $allItems = collect();

        foreach ($items as $item) {
            $targetItem = $this->findDirectoryByName(basename($item));

            if (is_null($targetItem)) {
                $targetItem = $this->createItem($type, $item, $parentName);

                if (!$searchingForSubDirs) {
                    $this->all->push($targetItem);
                }
            }
            $allItems->push($targetItem);
        }

        return $allItems;
    }

    private function setUpDirectories(): void
    {
        $this->directories = $this->setUpItems($this->storage->directories(), self::DIRECTORY_TYPE);
    }

    private function setUpFiles(): void
    {
        $this->diskFiles = $this->setUpItems($this->storage->files(), self::FILE_TYPE);
    }

    private function getLastModified(string $item, string $type): string
    {
        $lastModifiedTimestamp = ($type === self::FILE_TYPE) ? $this->storage->lastModified($item) : "";
        if ($type === self::DIRECTORY_TYPE) {
            $items = $this->storage->files($item);
            if (!empty($items))  {
                $lastModifiedTimestamp = $this->storage->lastModified($items[0]);
            }
        }

        $lastModified = Carbon::createFromTimestamp($lastModifiedTimestamp)->timezone(config('app.timezone'));

        return $lastModified->format(ConfigRepository::getModifiedFileTimeFormat());
    }

    private function getFileExtension(string $type, string $item): string
    {
        return ($type === self::FILE_TYPE) ? pathinfo($item, PATHINFO_EXTENSION) : "";
    }

    private function searchRecursiveForDirectory(Collection $items, string $directoryName): Item|null
    {
        foreach ($items as $item) {
            if ($item->name === $directoryName) {
                return $item;
            }

            if ($item->subDir->isNotEmpty()) {
                $found = $this->searchRecursiveForDirectory($item->subDir, $directoryName);
                if ($found) {
                    return $found;
                }
            }
        }

        return null;
    }

    private function findByParentName(Collection $items, string $parent): Collection
    {
        $foundItems = collect();

        foreach ($items as $item) {
            if ($item->parent === $parent) {
                $foundItems->push($item);
            }

            if ($item->subDir->isNotEmpty()) {
                $foundItems = $foundItems->merge($this->findByParentName($item->subDir, $parent));
            }
        }

        return $foundItems;
    }

    private function getFileUrl(string $type, string $item): string
    {
        if ($type !== self::FILE_TYPE) {
            return "";
        }

        if (!ConfigRepository::isACLEnabled()) {
            return "";
        }

        return Auth::user()->hasPermission('read') ? $this->storage->url($item) : "";
    }
}
