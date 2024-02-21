<?php

namespace Alireza\LaravelFileExplorer\Supports;

use Alireza\LaravelFileExplorer\Exceptions\ZipperException;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class Zipper extends ZipArchive
{
    /**
     * @var string
     */
    private string $diskName;

    /**
     * @var string
     */
    private string $zipName;

    /**
     * @var array
     */
    private array $items;

    /**
     * @var string
     */
    private string $zipPath;

    /**
     * Zipper constructor
     *
     * @param string $diskName
     * @param string $zipName
     * @param array $items
     */
    public function __construct(string $diskName, string $zipName, array $items)
    {
        $this->diskName = $diskName;
        $this->zipName = $zipName;
        $this->items = $items;
        $this->zipPath = storage_path($this->zipName);
    }

    /**
     * Creates a zip archive for the items
     *
     * @throws ZipperException
     */
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

    /**
     * Returns the path of the zip file
     *
     * @return string
     */
    public function getZipPath(): string
    {
        return $this->zipPath;
    }

    /**
     * Adds items to the zip archive
     *
     * @return void
     */
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
