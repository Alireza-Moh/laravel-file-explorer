<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class ItemUploaded
{
    public string $diskName;

    public string $path;

    public function __construct(string $diskName, string $path) {
        $this->diskName = $diskName;
        $this->path = $path;
    }
}
