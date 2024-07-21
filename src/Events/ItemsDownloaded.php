<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class ItemsDownloaded
{
    public string $diskName;

    public array $items;

    public function __construct(string $diskName, array $items)
    {
        $this->diskName = $diskName;
        $this->items = $items;
    }
}
