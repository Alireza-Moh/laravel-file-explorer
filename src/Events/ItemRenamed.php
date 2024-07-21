<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class ItemRenamed
{
    public string $diskName;

    public string $oldName;

    public string $newName;

    public string $oldPath;

    public string $newPath;

    public function __construct(string $diskName, array $itemData) {
        $this->diskName = $diskName;
        $this->oldName = $itemData["oldName"];
        $this->newName = $itemData["newName"];
        $this->oldPath = $itemData["oldPath"];
        $this->newPath = $itemData["newPath"];
    }
}
