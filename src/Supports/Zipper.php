<?php

namespace AlirezaMoh\LaravelFileExplorer\Supports;

use AlirezaMoh\LaravelFileExplorer\Exceptions\ZipperException;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class Zipper extends ZipArchive
{
    private string $diskName;

    private string $zipName;

    private array $items;

    private string $zipPath;

    public function __construct(string $diskName, string $zipName, array $items)
    {
        $this->diskName = $diskName;
        $this->zipName = $zipName;
        $this->items = $items;
        $this->zipPath = storage_path($this->zipName);
    }

    public function zip(): void
    {
        $zipCreated = $this->open($this->zipPath, ZipArchive::CREATE);

        if ($zipCreated === true) {
            $this->addFiles();
            $this->close();
        }
        else {
            throw new ZipperException($zipCreated);
        }
    }

    public function getZipPath(): string
    {
        return $this->zipPath;
    }

    private function addFiles(): void {
        $storage = Storage::disk($this->diskName);

        foreach ($this->items as $item) {
            $itemPath = $item["path"];
            $this->addFromString(
                $itemPath,
                $storage->get($itemPath)
            );
        }
    }
}
