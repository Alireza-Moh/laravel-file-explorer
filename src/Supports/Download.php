<?php

namespace Alireza\LaravelFileExplorer\Supports;

use Alireza\LaravelFileExplorer\Supports\Traits\DownloadManager;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Download
{
    use DownloadManager;

    /**
     * @var string
     */
    private string $diskName;

    /**
     * @var array
     */
    private array $items;

    /**
     * @var Filesystem
     */
    private Filesystem $storage;

    /**
     * BaseDownload constructor
     *
     * @param string $diskName
     * @param array $items
     */
    public function __construct(string $diskName, array $items)
    {
        $this->diskName = $diskName;
        $this->items = $items;
        $this->storage = Storage::disk($diskName);
    }

    /**
     * Download all items
     *
     * @return BinaryFileResponse|StreamedResponse|JsonResponse
     */
    public function download(): BinaryFileResponse|StreamedResponse|JsonResponse
    {
        if (count($this->items) > 1) {
            return $this->downloadMultiple();
        }
        return $this->downloadSingle();
    }

    /**
     * Download a single item
     *
     * @return BinaryFileResponse|StreamedResponse|JsonResponse
     */
    private function downloadSingle(): StreamedResponse|BinaryFileResponse|JsonResponse
    {
        $item = $this->items[0];
        if ($item["type"] === "dir") {
            return $this->downloadMultiple();
        }

        return $this->storage->download($item["path"]);
    }

    /**
     * Download a multiple items
     *
     * @return BinaryFileResponse|StreamedResponse|JsonResponse
     */
    private function downloadMultiple(): BinaryFileResponse|StreamedResponse|JsonResponse
    {
        list($filteredItems, $filteredDirs) = $this->sortByType();

        foreach ($filteredDirs as $dir) {
            $filteredItems = array_merge($filteredItems, $this->getDirItems($dir));
        }
        $this->items = $filteredItems;

        return $this->zipItems();
    }

    /**
     * Get items from a directory
     *
     * @param array $dir
     * @return array
     */
    private function getDirItems(array $dir): array
    {
        $dirItems = $this->storage->allFiles($dir["path"]);
        $items = [];
        foreach ($dirItems as $dirItem) {
            $items[] = [
                "name" => basename($dirItem),
                "path" => $dirItem,
                "type" => "file"
            ];
        }
        return $items;
    }
}
