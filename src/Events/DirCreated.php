<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class DirCreated
{
    private string $diskName;

    private string $parentDirName;

    private string $path;

    public function __construct(string $diskName, string $parentDirName, string $path)
    {
        $this->diskName = $diskName;
        $this->parentDirName = $parentDirName;
        $this->path = $path;
    }

    public function getDiskName(): string
    {
        return $this->diskName;
    }

    public function getParentDirName(): string
    {
        return $this->parentDirName;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
