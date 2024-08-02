<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class ItemUploaded
{
    public string $diskName;

    public string $parentDirName;

    public string $itemName;

    public string $itemPath;

    public function __construct(string $diskName, string $parentDirName, string $itemName, string $itemPath) {
        $this->diskName = $diskName;
        $this->parentDirName = $parentDirName;
        $this->itemName = $itemName;
        $this->itemPath = $itemPath;
    }
}
