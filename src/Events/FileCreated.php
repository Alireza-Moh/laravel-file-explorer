<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class FileCreated
{
    public string $diskName;

    public string $parentDirName;

    public string $path;

    public function __construct(string $diskName, string $parentDirName, string $path)
    {
        $this->diskName = $diskName;
        $this->parentDirName = $parentDirName;
        $this->path = $path;
    }
}
