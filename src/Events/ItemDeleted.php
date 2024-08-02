<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class ItemDeleted
{
    public string $diskName;

    public string $itemName;

    public string $path;

    public function __construct(string $diskName, string $itemName, string $path)
    {
        $this->diskName = $diskName;
        $this->itemName = $itemName;
        $this->path = $path;
    }
}
